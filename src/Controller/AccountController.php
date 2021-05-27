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


class AccountController extends AbstractController
{ // @IsGranted("ROLE_ARTIST", "ROLE_ADMIN")
    // Mise a jour de son Profil
    /**
     * @Route("/account/update/{id}", name="update_", requirements={"id":"\d+"},  methods = {"GET", "POST"})
     */
    public function update(int $id): Response
    {
        $errors = [];

        $em = $this->getDoctrine()->getManager(); // Connexion
        $artist = $em->getRepository(Artist::class)->find($id);
        $styles = $em->getRepository(Style::class)->findAll();
        $artist_style = $artist->getStyles();


        if (!empty($_POST)) { // Mon formulaire n'est pas vide

            $safe = $_POST;

            // Je vérifie mes différents champs            
            if (strlen($safe['lastname']) < 2 || strlen($safe['lastname']) > 80) {
                $errors[] = 'Votre nom doit comporter entre 2 et 80 caracteres ';
            }
            if (strlen($safe['firstname']) < 2 || strlen($safe['firstname']) > 80) {
                $errors[] = 'Votre prenom doit comporter entre 2 et 80 caracteres ';
            }
            if (strlen($safe['tattoo_shop']) < 1 || strlen($safe['tattoo_shop']) > 80) {
                $errors[] = 'Le nom de votre salon  doit comporter entre 1 et 100 caracteres ';
            }
            if (strlen($safe['pseudo']) < 5 || strlen($safe['pseudo']) > 80) {
                $errors[] = 'Votre pseudo doit comporter entre 5 et 80 caracteres ';
            }
            if (strlen($safe['city']) < 1 || strlen($safe['city']) > 100) {
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
    
    
    
    
                $target_dir = $this->getParameter('cover_directory') . "/"; // uploads directory
                $file2 = basename($_FILES['cover_picture']['name']);
                $target_file = $target_dir . $file2;
                $max_size = 5242880;
                $size2 = $_FILES['cover_picture']['size'];
                $imageFileType2 = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $extensions2 = array('png', 'gif', 'jpg', 'jpeg');
                $tmp_name2 = $_FILES["cover_picture"]["tmp_name"];
    
                // verification de l'extension du fichier à uploader
                if (!in_array($imageFileType2, $extensions2)) {
                    $errors[] = "l'extension du fichier n'est pas reconnu ['png', 'gif', 'jpg', 'jpeg']" . $file;
                }
    
                // verification de la taille du fichier à uploader
                if ($size2 > $max_size) {
                    $errors[] = 'La taille du fichier dépasse la taille maxi ' . $max_size;
                }
    
                // Si pas d'erreurs, alors on upload le fichier
                if (count($errors) == 0) {
                    // Génère un identifiant unique
                    $fichier2 =  uniqid() . $imageFileType2;
    
                    // On va copier le fichier dans le dossier upload
                    $newfile2 = $target_dir . $fichier2;
                    if (!move_uploaded_file($tmp_name2, $newfile2)) {
                        $errors[] = 'Une erreur grave est survenue';
                    }
                   
                   
                }
                 
            }
            // je verifie mon $_files avec mes differentes contraintes, format, taille 
            if (count($errors) === 0) {


                $artist->setRoles(['ROLE_ARTIST']);
                $artist->setLastName($safe['lastname']);
                $artist->setFirstName($safe['firstname']);
                $artist->setPseudo($safe['pseudo']);
                $artist->setTattooShop($safe['tattoo_shop']);
                $artist->setCity($safe['city']);
                $artist->setAddress($safe['address']);
                $artist->setDescription($safe['description']);
                $artist->setInstagram($safe['instagram']);
                $artist->setSiret($safe['siret']);
                $artist->setCoverPicture($fichier2);
                $artist->setProfilePicture($fichier);



                $geocoder = new \OpenCage\Geocoder\Geocoder('89c46309bda04d75a85786956180d2cb');
                $geoResult = $geocoder->geocode($safe['address'] . ' ' . $safe['city']);
                if ($geoResult && $geoResult['total_results'] > 0) {
                    $first = $geoResult['results'][0];
                    // dd($first);

                    $geopoints = [
                        'lng' => $first['geometry']['lng'],
                        'lat' => $first['geometry']['lat'],
                    ];

                    $artist->setLat($geopoints['lat']);
                    $artist->setLng($geopoints['lng']);
                    // dd($geopoints);
                }

                // Pour rajouter chque style coche au tableau de styles il faut: 
                $styles_artist = $em->getRepository(Style::class)->findBy(['id' => $safe['style']]);
                //dd($styles_artist);
                foreach ($styles_artist as $style) {
                    $artist->addStyle($style);
                }
                if (isset($safe['re-style'])) {
                    $styles_delete = $em->getRepository(Style::class)->findBy(['id' => $safe['re-style']]);

                    foreach ($styles_delete as $styles) {

                        $artist->removeStyle($styles);
                    }
                }


                $em->flush(); // Execute la requete (equivalent du $bdd->execute())
                $this->addFlash('success', 'Super! Votre compte a bien ete mis a jour!');
                return $this->redirectToRoute('profil_', ['id' => $artist->getId()]);
            } else {
                // J'ai des erreurs, je les affiche via le flash message
                $this->addFlash('danger', implode(' - ', $errors));
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
     * @Route("/profil/{id}", name="profil_", methods={"GET","POST"}, requirements={"id":"\d+"})
     */
    public function show(int $id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $artist = $em->getRepository(Artist::class)->find($id);
        $form = $this->createForm(AccountType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('pictures')->getData();

            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('pictures_directory'),
                    $fichier
                );

                // On stocke l'image dans la base de données (son nom)
                $picture = new Picture();
                $picture->setName($fichier);
                $picture->setDate(new \DateTime('now'));
                $artist->addPicture($picture);
            }


            $em->persist($artist);
            $em->flush();

            return $this->redirectToRoute('profil_', ['id' => $artist->getId()]);
        }

        return $this->render('account/profile.html.twig', [
            'artist' => $artist,
            'form' => $form->createView(),
        ]);
    }
}
