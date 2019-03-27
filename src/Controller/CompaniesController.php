<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Company;
use App\Entity\Address;

class CompaniesController extends AbstractController
{
    /**
     * @Route("/companies/{page}", name="companies")
     */
    public function index($page,ObjectManager $manager)
    {
        
        $repo = $manager->getRepository(Company::class);

        $result = $repo->findAllPagineTrie($page,5);

        return $this->render('companies/index.html.twig', [
            'companies' => $result['list'],
            'nbPages'   => intval($result['nbPages']),
            'page' => intval($page)
        ]);
    }



    /**
     * 
     * @Route("/company/detail/{id}", name="company_detail")
     */
    public function detail($id,Company $company,ObjectManager $manager,Request $request)
    {
        $dateFiltre = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $formDateVersion = $this->createForm(DateTimeType::class, $dateFiltre, ['with_seconds' => true]);
        $formDateVersion->handleRequest($request);

        $IsVersion = false;
        if($formDateVersion->isSubmitted() && $formDateVersion->isValid()){
            $IsVersion = true;

            $date = $formDateVersion->get('date')->getData();
            $time = $formDateVersion->get('time')->getData();
            $dateFiltre = $date['year'] . '-' . $date['month'] . '-' . $date['day']. ' ' 
                        . $time['hour']. ':' . $time['minute']. '-' . $time['second'];
            
            $gedmo = $manager->getRepository(LogEntry::class);
    
            $queryCompany = $gedmo
                ->createQueryBuilder('version')
                ->where('version.objectClass = :CLASS')
                ->andWhere('version.objectId = :ID')
                ->andWhere('version.loggedAt <= :DATE')
                ->setParameter('CLASS', Company::class)
                ->setParameter('ID', $company->getId())
                ->setParameter('DATE', $dateFiltre)
                ->orderBy('version.loggedAt', 'DESC')
                ->setMaxResults(1);
    
            $version = $queryCompany->getQuery()->getOneOrNullResult();
    
            $addresses = [];
            if($version != null){
                $gedmo->revert($company,$version->getVersion());
    
                foreach ($company->getAllAddresses() as $address) {
                    $query = $gedmo
                        ->createQueryBuilder('version')
                        ->where('version.loggedAt <= :DATE')
                        ->andWhere('version.objectClass = :CLASS')
                        ->andWhere('version.objectId = :ID')
                        ->setParameters([
                            'DATE'  => $dateFiltre,
                            'CLASS' => Address::class,
                            'ID'    => $address->getId(),
                        ])
                        ->orderBy('version.loggedAt', 'DESC')
                        ->setMaxResults(1);
    
                    $versionAddress = $query->getQuery()->getOneOrNullResult();
                    if(!is_null($versionAddress)){
                        $gedmo->revert($address, $versionAddress->getVersion());
                        $addresses[] = $address;
                    }
                }
                $company->setAddresses($addresses);
            } else {
                $company = null;
            }
        }
        
        return $this->render('companies/detail.html.twig', [
            'company' => $company,
            'formDateVersion'  => $formDateVersion->createView(),
            'isVersion' => $IsVersion,
            'dateFiltre' => $dateFiltre
        ]);
    }
}
