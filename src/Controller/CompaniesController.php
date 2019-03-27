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

        $companies = $repo->findAllPagineTrie($page,5);

        return $this->render('companies/index.html.twig', [
            'companies' => $companies,
            'page' => $page
        ]);
    }



    /**
     * @Route("/company/detail/{id}/version", name="company_detail_version")
     * @Route("/company/detail/{id}", name="company_detail")
     */
    public function detail($id,Company $company=null,ObjectManager $manager,Request $request)
    {
        $dateFiltre = \DateTime::createFromFormat('Y-m-d H:i:s', '2019-03-26 13:13:00');
        $formDateVersion = $this->createForm(DateTimeType::class, null, ['with_seconds' => true]);
        dump($formDateVersion);
        $formDateVersion->handleRequest($request);
        dump($dateFiltre);
        if($company == null){
            $company = new Company();
        }

            $IsVersion = false;
            if($formDateVersion->isSubmitted() && $formDateVersion->isValid()){
                $IsVersion = true;
            
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
        
                    foreach ($company->getAddresses() as $address) {
                        dump($address);
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
                    $company->removeAllAddresses();
        
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
