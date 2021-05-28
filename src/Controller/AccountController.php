<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Style;
use App\Entity\Picture;
use App\Form\AccountType;

use App\Controller\AccountBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AccountController extends AccountBaseController
{ 
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/account/update/", name="update_",  methods = {"GET", "POST"})
     * 
     */
    public function update(): Response
    {
        $errors = [];
        
        // if(!$this->getUser()){
            //     // Utilisateur non connecté
            //      retun $this->redirectToRoute('some_url')
            // }
            
            
            $em = $this->getDoctrine()->getManager(); // Connexion
            $artist = $em->getRepository(Artist::class)->find($this->getUser());
            $styles = $em->getRepository(Style::class)->findAll();
            $artist_style = $artist->getStyles();
            
            if (!empty($_POST)) { // Mon formulaire n'est pas vide
                
                // $post = [];
                // foreach($_POST as $key => $value){
                //     if(is_array($value)){
                //         $post[$key] = array_map('trim', array_map('strip_tags', $value));
                //     }
                //     else {
                //         $post[$key] = trim(strip_tags($value));
                //     }
                // }
            $safe = $_POST;
            $errors = array();
            // Je vérifie mes différents champs            

            //dd($safe['lastname']);
            $this->ValidateAlphanumericInput($safe['lastname'], 2, 80, "nom", $errors);
            $this->ValidateAlphanumericInput($safe['firstname'], 2, 80, "prénom", $errors);
            $this->ValidateAlphanumericInput($safe['tattoo_shop'], 1, 100, "nom de votre salon", $error);
            $this->ValidateAlphanumericInput($safe['pseudo'], 3, 80, "pseudo", $errors);
            $this->ValidateAlphanumericInput($safe['city'], 1, 80, "ville", $errors);
            $this->ValidateNumericInput($safe['siret'], 14, 14, "siret", $errors);
            $this->ValidateGeneralInput($safe['description'], 15, 500, "description", $errors);


            // si styles est vide et que le nombre de style est deja egal a 4
            if(empty($safe['styles_value']) &&  !isset($safe['style']) && count($safe['styles_value']) == 4 && count($safe['style']) > 4){
                 array_push($errors, "Vous devez choisir entre 1 et 4 styles.");
            }
            //dd($safe["profile_picture_value"]));
            // dd((empty($_FILES['profile_picture'])));
            if (empty($safe['profile_picture_value']) || !empty($_FILES['profile_picture']["name"])) {

                $fichier = $this->UploadImage($_FILES['profile_picture'], 'images_directory', $errors);
            }
            if (empty($safe['cover_picture_value']) || !empty($_FILES['cover_picture']["name"])) {

                $fichier2 = $this->UploadImage($_FILES['cover_picture'], 'cover_directory', $errors);
            }
            // dd($errors);
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
                
                if(!empty($fichier2)){
                 $artist->setCoverPicture($fichier2);
                 }
                if (!empty($fichier)) {
                    $artist->setProfilePicture($fichier);
                }




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
                if (isset($safe['style'])) {
                    $styles_artist = $em->getRepository(Style::class)->findBy(['id' => $safe['style']]);
                    //dd($styles_artist);
                    foreach ($styles_artist as $style) {
                        $artist->addStyle($style);
                    }
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
