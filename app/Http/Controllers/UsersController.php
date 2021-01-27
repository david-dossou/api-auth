<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\User;

class UsersController extends Controller
{

    /**
     * Enregistrer un nouvelle utilisateur et retourner le resultat 
     *
     * @return json
     */

    public function createUser() {

        // Declaration et recuperation des variables a enregistrer
        $last_name_user = NULL;
        $first_name_user = NULL;
        $telephone_user = NULL;
        $email_user = NULL;
        $password_user = NULL;
        $user_id = NULL;
        $token_user = Str::random(300);

        if(\request('last_name_user')) {
            $last_name_user = strtoupper(\request('last_name_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le nom de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('first_name_user')) {
            $first_name_user = strtoupper(\request('first_name_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le prenom de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('telephone_user')) {
            $telephone_user = \request('telephone_user');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le téléphone de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('email_user')) {
            $email_user = \request('email_user');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'email de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('password_user')) {
            $password_user = bcrypt((\request('password_user')));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le mot de passe de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('user_id')) {
            $user_id = \request('user_id');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur qui fait l'operation est manquant !"
            ], 403);
        }

        // Enregistrement de l'utilisateur
        $add_user = User::create([
            'first_name_user' => $first_name_user,
            'last_name_user' => $last_name_user,
            'telephone_user' => $telephone_user,
            'email_user' => $email_user,
            'password_user' => $password_user,
            'user_id' => $user_id, 
            'token_user' => $token_user
        ]);

        //retourner le resultat de l'enregistrement
        if($add_user) {
            $accessToken = $add_user->createToken('authToken')->accessToken;
            return \response([
                "resultat" => $add_user,
                "statut" => "succes",
                "message" => "L'utilisateurs à été enregistré avec succès",
                "accessToken" => $accessToken
            ]);
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Une erreur est survenue lors de l'operation !"
            ], 403);
        }
        

    }






    /**
     * Connexion d'un utilisateur
     *
     * @return json
     */

    public function login() {
        // Declaration et recuperation des variables
        $email_user = NULL;
        $password_user = NULL;

        if(request('email_user')) {
            $email_user = request('email_user');
        } else {
            return resppnse([
                "result" => [],
                "state" => "error",
                "message" => "L'email est manquant !"
            ]);
        }

        if(request('password_user')) {
            $password_user = request('password_user');
        } else {
            return resppnse([
                "result" => [],
                "state" => "error",
                "message" => "Le mot de passe est manquant !"
            ]);
        }

        // Tentative de connexion
        $user = auth()->attempt(['email_user' => $email_user, 'password' => $password_user]);
        if($user) {
            $data = auth()->user();

            // Verifier si le compte de l'utilisateur a été sipprimé ou bloqué 
            if($data->delete_user != 0) {
                return \response([
                    "resultat" => [],
                    "statut" => "erreur",
                    "message" => "Ce compte a été supprimé! la connexion lui est interdite !"
                ], 403);
            }
            
            // Verifier que son compte est bloqué
            if($data->state_user != 0) {
                return \response([
                    "resultat" => [],
                    "statut" => "erreur",
                    "message" => "Ce compte n'est pas autorisé a se connecter !"
                ], 403);
            }


            $accessToken = $data->createToken('authToken')->accessToken;
            return \response([
                "resultat" => $data,
                "statut" => "succes",
                "message" => "L'utilisateurs est authentifié",
                "accessToken" => $accessToken
            ]);

        } else {
            return response([
                "result" => [],
                "state" => "error",
                "message" => "Email ou mot de passe incorrect !"
            ]);
        }


    }


    /**
     * Lister les utilisateur
     *
     * @return json
     */


    public function listeUsers() {
        $users = DB::select("SELECT us.*, administre.first_name_user as prenom_admin, 
        administre.last_name_user as nom_admin 
        FROM users as us 
        INNER JOIN users as administre ON us.user_id=administre.id 
        WHERE us.delete_user=0 AND us.state_user=0 ORDER BY us.created_at DESC 
        ");

        return \response([
            "resultat" => $users,
            "statut" => "succes",
            "message" => "Liste des utilisateurs",
        ]);
    }


    /**
     * Modifier utilisateur et retourner le resultat 
     *
     * @return json
     */

    public function updateUser() {

        // Declaration et recuperation des variables a enregistrer
        $token_user = NULL;
        $last_name_user = NULL;
        $first_name_user = NULL;
        $telephone_user = NULL;
        $email_user = NULL;
        $user_edite_id = NULL;

        if(\request('token_user')) {
            $token_user = strtoupper(\request('token_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur est manquant !"
            ], 403);
        }

        if(\request('last_name_user')) {
            $last_name_user = strtoupper(\request('last_name_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le nom de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('first_name_user')) {
            $first_name_user = strtoupper(\request('first_name_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le prenom de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('telephone_user')) {
            $telephone_user = \request('telephone_user');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le téléphone de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('email_user')) {
            $email_user = \request('email_user');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'email de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('user_edite_id')) {
            $user_edite_id = \request('user_edite_id');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur qui fait l'operation est manquant !"
            ], 403);
        }

        //Rechercher l'utilisateur et modifier
        $user = User::where('token_user', $token_user)->first();

        $token_user = Str::random(300);
        
        $update_user = $user->update([
            'first_name_user' => $first_name_user,
            'last_name_user' => $last_name_user,
            'telephone_user' => $telephone_user,
            'email_user' => $email_user,
            'user_edite_id' => $user_edite_id, 
            'token_user' => $token_user
        ]);

        //retourner le resultat de l'enregistrement
        if($update_user) {
            $user = User::where('token_user', $token_user)->first();
            $accessToken = $user->createToken('authToken')->accessToken;
            return \response([
                "resultat" => $user,
                "statut" => "succes",
                "message" => "L'utilisateurs à été enregistré avec succès",
                "accessToken" => $accessToken
            ]);
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Une erreur est survenue lors de l'operation !"
            ], 403);
        }
        

    }




    /**
     * Modifier mot de passe utilisateur et retourner le resultat 
     *
     * @return json
     */

    public function updatePasswordUser() {

        // Declaration et recuperation des variables a enregistrer
        $token_user = NULL;
        $password_user = NULL;
        $user_edite_id = NULL;

        if(\request('token_user')) {
            $token_user = strtoupper(\request('token_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur est manquant !"
            ], 403);
        }

        if(\request('password_user')) {
            $password_user = bcrypt(\request('password_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Le mot de passe de l'utilisateur est manquant !"
            ], 403);
        }

        if(\request('user_edite_id')) {
            $user_edite_id = \request('user_edite_id');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur qui fait l'operation est manquant !"
            ], 403);
        }

        //Rechercher l'utilisateur et modifier
        $user = User::where('token_user', $token_user)->first();

        $token_user = Str::random(300);
        
        $update_user = $user->update([
            'password_user' => $password_user,
            'user_edite_id' => $user_edite_id, 
            'token_user' => $token_user
        ]);

        //retourner le resultat de l'enregistrement
        if($update_user) {
            $user = User::where('token_user', $token_user)->first();
            return \response([
                "resultat" => $user,
                "statut" => "succes",
                "message" => "Le mot de passe de l'utilisateurs à été enregistré avec succès",
            ]);
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Une erreur est survenue lors de l'operation !"
            ], 403);
        }
        

    }



    /**
     * Supprimer utilisateur et retourner le resultat 
     *
     * @return json
     */

    public function deleteUser() {

        // Declaration et recuperation des variables a enregistrer
        $token_user = NULL;
        $delete_user = 1;
        $user_delete_id = NULL;
        $date_delete_user = date("Y-m-d H:i:s");

        if(\request('token_user')) {
            $token_user = strtoupper(\request('token_user'));
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur est manquant !"
            ], 403);
        }


        if(\request('user_delete_id')) {
            $user_delete_id = \request('user_delete_id');
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "L'utilisateur qui fait l'operation est manquant !"
            ], 403);
        }

        //Rechercher l'utilisateur et modifier
        $user = User::where('token_user', $token_user)->first();
        
        $update_user = $user->update([
            'delete_user' => $delete_user,
            'user_delete_id' => $user_delete_id, 
            'date_delete_user' => $date_delete_user
        ]);

        //retourner le resultat de l'enregistrement
        if($update_user) {
            return \response([
                "resultat" => [],
                "statut" => "succes",
                "message" => "L'utilisateur a été supprimé avec succès",
            ]);
        } else {
            return \response([
                "resultat" => [],
                "statut" => "erreur",
                "message" => "Une erreur est survenue lors de l'operation !"
            ], 403);
        }
        

    }






}
