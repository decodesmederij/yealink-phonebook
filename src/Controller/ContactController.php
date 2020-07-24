<?php
/*
 * Copyright 2014 IMAGIN Sp. z o.o. - imagin.pl
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\XMLFileService;
use App\Form\DataEditForm;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Form\Forms;

class ContactController extends AbstractController
{
    protected $form;

    /**
     * @param Request $request
     * @param $name
     * @return string
     */
    public function indexAction(Request $request, $name)
    {
        if ($request->headers->get('referer') === null) {
            $redirected = true;
        } else {
            $redirected = false;
        }

        $newPhoneBookForm = $this->generateFormModel($name);
        $url = $this->generateUrl('contact_backup', array('name' => $name));
        $newPhoneBookForm->setAction($url)
            ->setMethod('POST');
        $newPhoneBookForm = $newPhoneBookForm->getForm();
        $this->form = $newPhoneBookForm;

        return $this->render(
            'Contact/index.html.twig',
            array(
                'name' => $name,
                'redirected' => $redirected,
                'phone_book' => $newPhoneBookForm->createView(),

            )
        );
    }

    /**
     * @param string $name
     * @return string
     */
    public function listAction($name = '')
    {
        $pb = $this->form->createView();
        return $this->render('Contact/list.html.twig', array('phone_book' => $pb));
    }

    /**
     * @param string $name
     * @return string
     */
    public function editAction($name = '')
    {
        return $this->render('Contact/edit.html.twig');
    }

    /**
     * @param string $name
     * @return string
     */
    public function urlAction($name = '')
    {
        $directoryName = "pb";

        $remote_url = $this->generateUrl(
                'homepage',
                array(),
                UrlGenerator::ABSOLUTE_URL
            ) . $directoryName . '/' . $name;

        return $this->render('Contact/url.html.twig', array('remote_url' => $remote_url));
    }

    /**
     * @param Request $request
     * @param $name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function backupAction(Request $request, $name)
    {
        $this->savingProcess($request, $name, true);

        return $this->redirect($this->generateUrl('contact', array('name' => $name)));
    }

    public function saveAction(Request $request, $name)
    {
        $this->savingProcess($request, $name, false);

        return new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    protected function savingProcess($request, $name, $createArchive)
    {
        $filePath = "pb";
        $fileName = $name;

        if ($request->getMethod() !== 'POST') {
            throw new \Exception('No data sended.');
        }

        $dataTable = $request->request->all();

        $XMLPattern = new XMLFileService($fileName, $filePath);
        $XMLToSave = $XMLPattern->generateProperXML($dataTable['phone_book']);

        if ($createArchive) {
            $XMLPattern->archiveFile();
        }

        $XMLPattern->saveFile($XMLToSave);
    }
    /**
     * @param $name
     * @return mixed
     */
    protected function generateFormModel($name)
    {
        $XMLService = new XMLFileService($name, 'pb');
        $FileContent = $XMLService->loadFile();

        $createPhonebookForm = new DataEditForm(Forms::createFormFactory());

        return $createPhonebookForm->createForm($FileContent);
    }
}