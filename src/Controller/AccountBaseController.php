<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AccountBaseController extends AbstractController
{   // POur ceux qui vont regarder ce code :)

    // Ici on fait les tests des inputs dans les champs qui necessitent des caractères alphanumérique!
    protected function ValidateAlphanumericInput($input, $minLength, $maxLength, $fieldName, &$errorMessages)
    {   // une fonction régulière qui permet de n'avoir que des chiffres et des lettres y compris les accents etc
        //$minLength et $maxLength sont les valeurs minimum et maximum de nos inputs 
        // $errorMsg est le message a pusher dans notre tableau $errorMessage 
        $regularExpression = "/^[a-zA-ZÀ-ÿ0-9 ._'-]{".$minLength.","."$maxLength}+$/";
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
        
        $error = $this->ValidateInput($regularExpression,$errorMsg, $input); // ici on teste 
        if(!empty($error))
        {   
            //si notre tableau $error n'est pas vide alors envoie le message d'erreur dedans 
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
     // ICI on fait le test des uploads d'images , profil et couverture 
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
        // verification de l'extension du fichier à uploader
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
