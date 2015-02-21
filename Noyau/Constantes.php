<?php

// C'est ma classe "boite à outil", que j'utilise uniquement par des appels statiques !
// Rappel : nous sommes dans le répertoire Core, voilà pourquoi dans realpath je "remonte d'un cran" pour faire référence
// à la VRAIE racine de mon application
date_default_timezone_set('Europe/Paris');

final class Constantes
{
    // Les constantes relatives aux chemins

    const REPERTOIRE_VUES        = '/Vues/';

    const REPERTOIRE_EXCEPTIONS  = '/Noyau/Exceptions/';

    const REPERTOIRE_MODELE      = '/Modele/';

    const REPERTOIRE_MAPPERS     = 'DataMappers/';

    const REPERTOIRE_MAPPERS_SQL = 'Sql/';

    const REPERTOIRE_NOYAU       = '/Noyau/';

    const REPERTOIRE_CONTROLEURS = '/Controleurs/';

    const REPERTOIRE_TEST = '/Tests/';

    public static function repertoireRacine() {
        return realpath(__DIR__ . '/../');
    }

    public static function repertoireTests() {
        return self::repertoireRacine() . self::REPERTOIRE_TEST;
    }

    public static function repertoireVues() {
        return self::repertoireRacine() . self::REPERTOIRE_VUES;
    }

    public static function repertoireExceptions() {
        return self::repertoireRacine() . self::REPERTOIRE_EXCEPTIONS;
    }

    public static function repertoireModele() {
        return self::repertoireRacine() . self::REPERTOIRE_MODELE;
    }

    public static function repertoireMappers() {
        return self::repertoireModele() . self::REPERTOIRE_MAPPERS;
    }

    public static function repertoireMappersSql() {
        return self::repertoireMappers() . self::REPERTOIRE_MAPPERS_SQL;
    }

    public static function repertoireNoyau() {
        return self::repertoireRacine() . self::REPERTOIRE_NOYAU;
    }

    public static function repertoireControleurs() {
        return self::repertoireRacine() . self::REPERTOIRE_CONTROLEURS;
    }

    // Les constantes relatives aux sources de données

    const DEFAULT_DATABASE_NAME = 'catclinic';

    const DATABASE_CONFIG_FILE = 'Configuration/basededonnees.ini';

    const TABLE_UTILISATEUR = 'utilisateur';

    const TABLE_CHAT = 'chat';

    const TABLE_PROPRIETAIRE = 'proprietaire';

    const TABLE_VISITE = 'visite';

    const TABLE_PRATICIEN = 'praticien';
    
    const TABLE_ARTICLE = 'article';
    
    const TABLE_CATEGORIE = 'categorie';
    
    const TABLE_AUTEUR = 'auteur';

    // divers

    const NB_MAX_UTILISATEURS_PAR_PAGE = 5;
    
    const NB_MAX_ARTICLES_PAR_PAGE = 5;
    
    const NB_MAX_ARTICLES_ENLIGNE_PAR_PAGE = 5;
    
    const NB_MAX_CATEGORIES_PAR_PAGE = 5;
    
    const NB_MAX_AUTEURS_PAR_PAGE = 5;
    
    const NB_MAX_CHATS_PAR_PAGE = 5;
    
    const NB_MAX_PRATICIENS_PAR_PAGE = 5;
    
    const NB_MAX_PROPRIOS_PAR_PAGE = 5;
    
    const NB_MAX_VISITES_PAR_PAGE = 5;

    const DATE_FORMAT = 'Y-m-d H:i';
}