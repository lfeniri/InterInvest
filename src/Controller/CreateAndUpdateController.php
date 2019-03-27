<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;


use App\Entity\Company;
use App\Entity\LegalForm;
use App\Entity\Address;

use App\Form\CompanyType;

class CreateAndUpdateController extends AbstractController
{
    /**
     * @Route("/create/company", name="create_company")
     * @Route("/edit/company/{id}", name="edit_company")
     */
    public function index(Company $company=null,Request $request,ObjectManager $manager)
    {
        if($company == null){
            $company = new Company();
        }


        $form = $this->createForm(CompanyType::class, $company);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($company);
            $manager->flush();
            return $this->redirectToRoute('company_detail',['id' => $company->getId()]);
        }
       

        return $this->render('create_and_update/index.html.twig', [
            'formCompany' => $form->createView(),
            'isUpdate' => $company->getId() !== null
            ]);
    }
}
