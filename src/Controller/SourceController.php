<?php
/*
 * Copyright 2014 IMAGIN Sp. z o.o. - imagin.pl
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Form\NewFileForm;
use App\Form\UploadFileForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;

class SourceController extends AbstractController
{
    /**
     * It should show or redirect to default routing when file found
     */
    public function indexAction()
    {
        return $this->render(
            'Source/index.html.twig'
        );
    }

    /**
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function uploadAction(Request $request)
    {
        $createUploadFileForm = new UploadFileForm(Forms::createFormFactory());
        $uploadFileForm = $createUploadFileForm->createForm();

        if ($request->getMethod() === 'POST') {
            $uploadFileForm->bind($request);

            if ($uploadFileForm->isValid()) {
                $file = $request->files->get($uploadFileForm->getName());
                $path = "pb";
                $filename = $file['fileToUpload']->getClientOriginalName();

                $file['fileToUpload']->move($path, $filename);

                return $this->redirect(
                    $this->generateUrl('contact', array('name' => $filename))
                );
            }
        }

        return $this->render(
            'Source/upload.html.twig',
            array('uploadFileForm' => $uploadFileForm->createView(),)
        );
    }

    /**
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws
     */
    public function newAction(Request $request)
    {
        $createNewFileForm = new NewFileForm(Forms::createFormFactory());
        $newFileForm = $createNewFileForm->createForm();

        if ($request->getMethod() === 'POST') {
            $newFileForm->handleRequest();

            if ($newFileForm->isValid()) {
                $filename = $newFileForm->getData();
                $filename = $filename['fileName'];
                $XMLPattern = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><YealinkIPPhoneDirectory></YealinkIPPhoneDirectory>");

                if ($XMLPattern->saveXML('pb/' . $filename . '.xml') !== false) {
                    return $this->redirect(
                        $this->generateUrl('contact', array('name' => $filename . ".xml"))
                    );
                } else {
                    throw \Exception("Problem with save file into given directory");
                }
            }
        }

        return $this->render(
            'Source/new.html.twig',
            array(
                'newFileForm' => $newFileForm->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param String $name
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws
     */
    public function deleteAction(Request $request, $name) {
        unlink('pb/' . $name);

        return $this->redirect(
            $this->generateUrl('source')
        );
    }

    /**
     * @return string
     */
    public function listAction()
    {
        $sourceDirectory = "pb";
        $fileList = $this->searchXMLFiles($sourceDirectory);

        return $this->render(
            'Source/list.html.twig',
            array(
                'fileList' => $fileList,
            )
        );
    }

    /**
     * Searching for XML files under given directory
     *
     * @param $sourceDirectory
     * @return array|null
     */
    protected function searchXMLFiles($sourceDirectory)
    {
        $i = 0;
        $fileNamesArray = array();

        if (is_dir($sourceDirectory)) {
            foreach (glob($sourceDirectory . "/*.xml") as $file) {

                $fileParametersArray = explode("/", $file);
                $fileName = end($fileParametersArray);

                $fileNamesArray[$i]['name'] = $fileName;
                $fileNamesArray[$i]['modified'] = date("m.d.Y H:i:s", filemtime($sourceDirectory . "/" . $fileName));
                $i+=1;
            }
        } else {
            return null;
        }

        if (empty($fileNamesArray)) {
            return null;
        }
        $fileNamesArray = $this->files_sort($fileNamesArray, 'modified', SORT_DESC);

        return $fileNamesArray;
    }

    function files_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
}
