<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Style;
use App\Entity\Picture;
use App\Form\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountBaseController extends AbstractController
{
    protected function ValidateAlphanumericInput($input, $minLength, $maxLength, $fieldName, &$errorMessages)
    {
        $regularExpression = "/^[a-zA-Z]+[a-zA-Z0-9 ._]{".$minLength.","."$maxLength}+$/";
        $errorMsg = 'Votre  '.$fieldName.' doit comporter entre ' .$minLength.' et ' .$maxLength.'  caracteres ';
        
        $error = $this->ValidateInput($regularExpression,$errorMsg, $input);
        if(!empty($error))
        {
            array_push($errorMessages, $error);
        }
    }

    protected function ValidateNumericInput($input, $minLength, $maxLength, $fieldName, &$errorMessages)
    {
        $regularExpression="/^[0-9]{".$minLength.","."$maxLength}+$/";
        $errorMsg = 'Votre  '.$fieldName.' doit comporter ' .$minLength.' caracteres ';
        
        $error = $this->ValidateInput($regularExpression,$errorMsg, $input);
        if(!empty($error))
        {
            array_push($errorMessages, $error);
        }
        
    }

    protected function ValidateGeneralInput($input, $minLength, $maxLength, $fieldName, &$errorMessages)
    {
        $regularExpression = "/^[\W\d\w]{".$minLength.","."$maxLength}+$/";
        $errorMsg = 'Votre  '.$fieldName.' doit comporter entre ' .$minLength.' et ' .$maxLength.'  caracteres ';
        
        $error = $this->ValidateInput($regularExpression,$errorMsg, $input);
        if(!empty($error))
        {
            array_push($errorMessages, $error);
        }
    }

    private function ValidateInput($regularExpression, $errorMessage, $input)
    {
            if(!preg_match($regularExpression, $input))
            {
               // dd($input);
                //dd(preg_match($regularExpression, $input));
                return $errorMessage;
            }
    }

    protected function UploadImage($uploads, $directory, &$errorMessage)
    {


        //dd($errorMessage);
        $target_dir = $this->getParameter($directory) . "/"; // uploads directory
        $file = basename($uploads['name']);
        $target_file = $target_dir . $file;
        $max_size = 5242880;
        $size = $uploads['size'];
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
       
        $extensions = array('png', 'gif', 'jpg', 'jpeg');
        $tmp_name = $uploads["tmp_name"];
        $errorCount = 0;
        // verification de l'extension du fichier à uploa3der
        if (!in_array($imageFileType, $extensions)) {
            array_push($errorMessage, "l'extension du fichier n'est pas reconnu ['png', 'gif', 'jpg', 'jpeg']" . $file);
            $errorCount++;
        }

        // verification de la taille du fichier à uploader
        if ($size > $max_size) {
            array_push($errorMessage, 'La taille du fichier dépasse la taille maxi ' . $max_size);
            $errorCount++;
        }
     
       // dd ("i am here2");
        // Si pas d'erreurs, alors on upload le fichier
        if ($errorCount == 0) {
            // Génère un identifiant unique
            $fichier =  uniqid() . '.' . $imageFileType;

            // On va copier le fichier dans le dossier upload
            $newfile = $target_dir . $fichier;
            if (!move_uploaded_file($tmp_name, $newfile)) {
                $errors[] = 'Une erreur grave est survenue';
            }
           
            //$errorMessage = [$errors];
           return $fichier; 
        }
    }
}
