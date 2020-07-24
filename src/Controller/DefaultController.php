<?php
/*
 * Copyright 2014 IMAGIN Sp. z o.o. - imagin.pl
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function indexAction()
    {
        $file = 'pb/contacts.xml';

        if (!file_exists($file)) {
            return $this->redirect($this->generateUrl('source'));
        } else {
            return $this->redirect($this->generateUrl('contact', array('name' => "contacts.xml")));
        }
    }
}