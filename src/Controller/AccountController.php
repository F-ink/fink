<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Style;
use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{

    //Creation de son profil et uploads de 0 a 6 
    /**
     * @Route("/account/create/{id}", name="account_create")
     */
    public function create(int $id, Style $styles): Response
    {
        $errors = [];

        $em = $this->getDoctrine()->getManager(); // Connexion
        $artist = $em->getRepository(Artist::class)->find($id);
        $styles = $em->getRepository(Style::class)->findAll();


        if (!empty($_POST)) { // Mon formulaire n'est pas vide
            $safe = array_map('trim', array_map('strip_tags', $_POST));

            // Je vérifie mes différents champs
            //J'utilise des regex pour raccourcir le code            
            if (!preg_match("/^[a-zA-Z-' ]*$/", $safe['lastname']) || strlen($safe['lastname']) < 2 || strlen($safe['lastname']) > 80) {
                $errors[] = 'Votre nom doit comporter entre 2 et 80 caracteres ';
            }
            if (!preg_match("/^[a-zA-Z-' ]*$/", $safe['firstname']) || strlen($safe['firstname']) < 2 || strlen($safe['firstname']) > 80) {
                $errors[] = 'Votre prenom doit comporter entre 2 et 80 caracteres ';
            }
            if (strlen($safe['tattoo_shop']) < 1 || strlen($safe['tattoo_shop']) > 80) {
                $errors[] = 'Le nom de votre salon  doit comporter entre 1 et 100 caracteres ';
            }
            if (!preg_match("/^[a-zA-Z-' ]*$/", $safe['city']) || strlen($safe['city']) < 2 || strlen($safe['city']) > 100) {
                $errors[] = 'La ville n\'est pas valide .';
            }

            if (!is_numeric($safe['siret']) || $safe['siret'] < 14) {
                $errors[] = 'Veuillez entrer les 14 chiffres de votre siret.';
            }
            if (strlen($safe['description']) < 15) {
                $errors[] = 'La description doit comporter au moins 15 caractères';
            }
            if (empty($safe['style']) || count([$safe['style']]) > 4) {
                $errors[] = 'Vous devez choisir entre 1 et 4 categories de style';
            }

            // je verifie mon $_files avec mes differentes contraintes, format, taille 
            if (!empty($_FILES)) {
                $target_dir = $this->getParameter('images_directory') . "/"; // uploads directory
                $file = basename($_FILES['profile_picture']['name']);
                $target_file = $target_dir . $file;
                $max_size = 5242880;
                $size = $_FILES['profile_picture']['size'];
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $extensions = array('png', 'gif', 'jpg', 'jpeg');
                $tmp_name = $_FILES["profile_picture"]["tmp_name"];

                // verification de l'extension du fichier à uploader
                if (!in_array($imageFileType, $extensions)) {
                    $errors[] = "l'extension du fichier n'est pas reconnu ['png', 'gif', 'jpg', 'jpeg']" . $file;
                }

                // verification de la taille du fichier à uploader
                if ($size > $max_size) {
                    $errors[] = 'La taille du fichier dépasse la taille maxi ' . $max_size;
                }

                // Si pas d'erreurs, alors on upload le fichier
                if (count($errors) == 0) {
                    // Génère un identifiant unique
                    $fichier =  uniqid() . '.' . $imageFileType;

                    // On va copier le fichier dans le dossier upload
                    $newfile = $target_dir . $fichier;
                    if (!move_uploaded_file($tmp_name, $newfile)) {
                        $errors[] = 'Une erreur grave est survenue';
                    }
                }
            }
            if (count($errors) === 0) {


                $artist->setLastName($safe['lastname']);
                $artist->setFirstName($safe['firstname']);
                $artist->setTattooShop($safe['tattoo_shop']);
                $artist->setCity($safe['city']);
                $artist->setAddress($safe['address']);
                $artist->setProfilePicture($fichier);
                $artist->setDescription($safe['description']);
                $artist->setInstagram($safe['instagram']);
                $artist->setSiret($safe['siret']);
                foreach ($styles as $style) {
                    $artist->addStyle($style);
                }
                $artist->setCreatedAt(new \DateTime('now')); // La date & heure de l'instant T

                $em->flush(); // Execute la requete (equivalent du $bdd->execute())
            }
        }

        return $this->render('account/index.html.twig', [
            'artist' => $artist,
            'styles'  => $styles
        ]);
    }


    // Mise a jour de son Profil
    /**
     * @Route("/account/update/{id}", name="account_update")
     */
    public function update(int $id, Style $style): Response
    {
        $errors = [];

        $em = $this->getDoctrine()->getManager(); // Connexion
        $artist = $em->getRepository(Artist::class)->find($id);
        $styles = $em->getRepository(Style::class)->findAll();
        $artist_style = $artist->getStyles();

        if (!empty($_POST)) { // Mon formulaire n'est pas vide
            $safe = array_map('trim', array_map('strip_tags', $_POST));

            // Je vérifie mes différents champs            
            if (!preg_match("/^[a-zA-Z-' ]*$/", $safe['lastname']) || strlen($safe['lastname']) < 2 || strlen($safe['lastname']) > 80) {
                $errors[] = 'Votre nom doit comporter entre 2 et 80 caracteres ';
            }
            if (!preg_match("/^[a-zA-Z-' ]*$/", $safe['firstname']) || strlen($safe['firstname']) < 2 || strlen($safe['firstname']) > 80) {
                $errors[] = 'Votre prenom doit comporter entre 2 et 80 caracteres ';
            }
            if (strlen($safe['tattoo_shop']) < 1 || strlen($safe['tattoo_shop']) > 80) {
                $errors[] = 'Le nom de votre salon  doit comporter entre 1 et 100 caracteres ';
            }
            if (strlen($safe['pseudo']) < 5 || strlen($safe['pseudo']) > 80) {
                $errors[] = 'Votre pseudo doit comporter entre 5 et 80 caracteres ';
            }
            if (!preg_match("/^[a-zA-Z-' ]*$/", $safe['city']) || strlen($safe['city']) < 2 || strlen($safe['city']) > 100) {
                $errors[] = 'La ville n\'est pas valide .';
            }

            if (!is_numeric($safe['siret']) || $safe['siret'] < 14) {
                $errors[] = 'Veuillez entrer les 14 chiffres de votre siret.';
            }
            if (strlen($safe['description']) < 15) {
                $errors[] = 'La description doit comporter au moins 15 caractères';
            }
            if (empty($safe['style']) || count([$safe['style']]) > 4) {
                $errors[] = 'Vous devez choisir entre 1 et 4 categories de style';
            }
            // je verifie mon $_files avec mes differentes contraintes, format, taille 
            if (!empty($_FILES)) {
                $target_dir = $this->getParameter('images_directory') . "/"; // uploads directory
                $file = basename($_FILES['profile_picture']['name']);
                $target_file = $target_dir . $file;
                $max_size = 5242880;
                $size = $_FILES['profile_picture']['size'];
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $extensions = array('png', 'gif', 'jpg', 'jpeg');
                $tmp_name = $_FILES["profile_picture"]["tmp_name"];

                // verification de l'extension du fichier à uploader
                if (!in_array($imageFileType, $extensions)) {
                    $errors[] = "l'extension du fichier n'est pas reconnu ['png', 'gif', 'jpg', 'jpeg']" . $file;
                }

                // verification de la taille du fichier à uploader
                if ($size > $max_size) {
                    $errors[] = 'La taille du fichier dépasse la taille maxi ' . $max_size;
                }

                // Si pas d'erreurs, alors on upload le fichier
                if (count($errors) == 0) {
                    // Génère un identifiant unique
                    $fichier =  uniqid() . '.' . $imageFileType;

                    // On va copier le fichier dans le dossier upload
                    $newfile = $target_dir . $fichier;
                    if (!move_uploaded_file($tmp_name, $newfile)) {
                        $errors[] = 'Une erreur grave est survenue';
                    }
                }
            }
            if (count($errors) === 0) {


                $artist->setLastName($safe['lastname']);
                $artist->setFirstName($safe['firstname']);
                $artist->setPseudo($safe['pseudo']);
                $artist->setTattooShop($safe['tattoo_shop']);
                $artist->setCity($safe['city']);
                $artist->setAddress($safe['address']);
                $artist->setProfilePicture($fichier);
                $artist->setDescription($safe['description']);
                $artist->setInstagram($safe['instagram']);
                $artist->setSiret($safe['siret']);

                foreach ($styles as $style) {
                    $artist->addStyle($style);
                }
                /* if($safe['re-style'] === false){
                  
                 $artist->removeStyle($safe['re-style']);
                }*/

                $em->flush(); // Execute la requete (equivalent du $bdd->execute())
            }
        }

        return $this->render('account/update.html.twig', [
            'artist' => $artist,
            'styles' => $styles,
            'artist_style' => $artist_style
        ]);
    }

    //Affichage du Profil

    /**
     * @Route("/profil/{id}", name="account_profil")
     */
    public function show(int $id): Response
    {
        $em = $this->getDoctrine()->getManager(); // Connexion
        $artist = $em->getRepository(Artist::class)->find($id);


        return $this->render('account/profile.html.twig', [
            'artist' => $artist
        ]);
    }
    /**
     * @Route("/picture/{id}", name="account_picture")
     */
    public function addPictures(int $id, Request $request): Response
    {  
    }
}
