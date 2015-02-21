<?php

class ProprietaireMapperTest extends Generic_Tests_DatabaseTestCase
{
    protected static $O_connexionViaFramework;

    protected static $O_proprietaireDeTest;

    protected static $O_mapperDeTest;

    protected static $S_cheminFixtures;

    protected static $O_chatDeTest;

    protected function setUp()
    {
    	parent::setUp();

        self::$O_proprietaireDeTest = new Proprietaire();
        self::$O_proprietaireDeTest->changeNom('Ferrandez');
        self::$O_proprietaireDeTest->changePrenom('Sébastien');

        self::$O_chatDeTest = new Chat();
        self::$O_chatDeTest->changeAge(new DateTime('1980-12-24'));
        self::$O_chatDeTest->changeNom('Patapon');
        self::$O_chatDeTest->changeTatouage('XXX000');
    }
	// setUpBeforeClass est appellée une seule fois avant le premier test de la classe
	// contrairement à setUp qui est appellée avant chaque test
    public static function setUpBeforeClass()
    {
        self::$O_connexionViaFramework = Connexion::recupererInstance('test');
        self::$O_mapperDeTest = FabriqueDeMappers::fabriquer('proprietaire', self::$O_connexionViaFramework);
        self::$S_cheminFixtures = dirname(__FILE__) . "/fixtures/proprietaires/";
    }
	// tearDownAfterClass est appellée après le dernier test de la classe
	// contrairement à tearDown qui est exécutée après chaque test
    public static function tearDownAfterClass()
    {
        self::$O_connexionViaFramework = null;
    }
	// getDataSet est imposée par la spécialisation de la classe de test de base de données PHPUnit
    public function getDataSet()
    {
        $dataset = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(self::$S_cheminFixtures . "proprietaires.yml");
        $dataset->addYamlFile(self::$S_cheminFixtures . "chats.yml");
        $dataset->addYamlFile(self::$S_cheminFixtures . "proprietaire_chat.yml");
        return $dataset;
    }
	// on teste l'insertion d'un propriétaire en base
	// avant l'insertion son id est vide et après il est valorisé
	// on asserte donc la présence d'un id forcément supérieur à 0
    public function testInsertionSimple() {
        self::$O_mapperDeTest->creer(self::$O_proprietaireDeTest);
        $this->assertGreaterThan(0, self::$O_proprietaireDeTest->donneIdentifiant());
    }
	// on sait que la recherche d'un propriétaire par un indentifiant invalide
	// déclenche la levée d'une exception
    public function testRechercheParIdentifiantInvalide() {
        $this->setExpectedException('Exception');
        self::$O_mapperDeTest->trouverParIdentifiant(-1);
    }
    // on vérifie le fait que l'id du propriétaire est bien celui demandé
    public function testRechercheParIdentifiantValide() {
    	$O_proprietaire = self::$O_mapperDeTest->trouverParIdentifiant(1);
    	$this->assertEquals(1, $O_proprietaire->donneIdentifiant());
    }
    // on modifie notre propriétaire donné par le setUp
    // pour vérifier qu'un propriétaire dont certaines propriétés manquent
    // ne peut être inséré et provoque la levée d'une exception
    public function testInsertionEchoueCausePrenom() {
        self::$O_proprietaireDeTest->changePrenom(null);
        $this->setExpectedException('Exception');
        self::$O_mapperDeTest->creer(self::$O_proprietaireDeTest);
    }
	// Même chose qu'au dessus mais avec le nom
    public function testInsertionEchoueCauseNom() {
        self::$O_proprietaireDeTest->changeNom(null);
        $this->setExpectedException('Exception');
        self::$O_mapperDeTest->creer(self::$O_proprietaireDeTest);
    }
	// on ajoute au propriétaire un chat, et on vérifie qu'après l'insertion
	// on retrouve ce chat en base
    public function testInsertionAvecUnChat() {
        $this->assertEquals(0, count(self::$O_proprietaireDeTest->donneChats()));
        self::$O_proprietaireDeTest->ajouteChat(self::$O_chatDeTest);
        $this->assertEquals(1, count(self::$O_proprietaireDeTest->donneChats()));
        
        self::$O_mapperDeTest->creer(self::$O_proprietaireDeTest);
        $queryTable = $this->getConnection()->createQueryTable(
        		'pc', 'SELECT * FROM proprietaire_chat where id_proprietaire = ' . self::$O_proprietaireDeTest->donneIdentifiant()
        );
        $this->assertEquals(count(self::$O_proprietaireDeTest->donneChats()), $queryTable->getRowCount());
        
    }
	// pareil qu'au dessus
    public function testInsertionAvecDeuxChats() {
    	$this->assertEquals(0, count(self::$O_proprietaireDeTest->donneChats()));
    	self::$O_proprietaireDeTest->ajouteChat(self::$O_chatDeTest);
    	$this->assertEquals(1, count(self::$O_proprietaireDeTest->donneChats()));
    	$O_autrechat = new Chat();
    	$O_autrechat->changeNom('Test');
    	$O_autrechat->changeAge(9);
    	$O_autrechat->changeTatouage('AAA000');
    	self::$O_proprietaireDeTest->ajouteChat($O_autrechat);
    	$this->assertEquals(2, count(self::$O_proprietaireDeTest->donneChats()));
    	
    	self::$O_mapperDeTest->creer(self::$O_proprietaireDeTest);
        $queryTable = $this->getConnection()->createQueryTable(
        		'pc', 'SELECT * FROM proprietaire_chat where id_proprietaire = ' . self::$O_proprietaireDeTest->donneIdentifiant()
        );
        $this->assertEquals(count(self::$O_proprietaireDeTest->donneChats()), $queryTable->getRowCount());
     }
    
    public function testSuppression() {
    	// on travaille sur le proprietaire issu des fixtures et plus de setUp
    	$O_proprietaire = self::$O_mapperDeTest->trouverParIdentifiant(1);
    	self::$O_mapperDeTest->supprimer($O_proprietaire);
    	$this->assertEquals(0, $this->getConnection()->getRowCount(Constantes::TABLE_PROPRIETAIRE));
    }
    
    public function testSuppressionAvecDeuxChats() {
    	// on travaille sur le proprietaire issu des fixtures
    	$O_proprietaire = self::$O_mapperDeTest->trouverParIdentifiant(1);
    	self::$O_mapperDeTest->supprimer($O_proprietaire);
    	$this->assertEquals(0, $this->getConnection()->getRowCount(Constantes::TABLE_PROPRIETAIRE));
    	$this->assertEquals(0, $this->getConnection()->getRowCount(Constantes::TABLE_CHAT));
    }
    
    public function testMiseAJourDunChat() {
    	$S_nom = 'Nom_modifié';
    	// on travaille sur le proprietaire issu des fixtures
    	$O_proprietaire = self::$O_mapperDeTest->trouverParIdentifiant(1);
    	$this->assertEquals(2, count($O_proprietaire->donneChats()));
    	$O_chat = $O_proprietaire->donneChats()[0];
    	$O_chat->changeNom($S_nom);
    	$O_proprietaire->ajouteChat($O_chat);
    	$this->assertEquals(2, count($O_proprietaire->donneChats()));
    	self::$O_mapperDeTest->actualiser($O_proprietaire);
    	$queryTable = $this->getConnection()->createQueryTable(
    			'chat', 'SELECT * FROM ' . Constantes::TABLE_CHAT . ' where id = ' . $O_chat->donneIdentifiant()
    	);
    	$dataSet = $this->getConnection()->createDataSet();
    	$expectedTable = $dataSet->getTable(Constantes::TABLE_CHAT)->getRow(0);
    	// on compare deux arrays ici
    	$this->assertEquals($expectedTable, $queryTable->getRow(0));
    }
    
    public function testSuppressionDunChat() {
    	// on travaille sur le proprietaire issu des fixtures
    	$O_proprietaire = self::$O_mapperDeTest->trouverParIdentifiant(1);
    	$this->assertEquals(2, count($O_proprietaire->donneChats()));
    	$O_chat = $O_proprietaire->donneChats()[1];

    	$O_proprietaire->enleveChat($O_chat);
    	$this->assertEquals(1, count($O_proprietaire->donneChats()));
    	
    	// suppression physique
    	self::$O_mapperDeTest->actualiser($O_proprietaire);
    	
    	// On vérifie que le changement est intervenu dans la base de données
    	$queryTable = $this->getConnection()->createQueryTable(
    			'pc', 'SELECT * FROM proprietaire_chat where id_proprietaire = ' . $O_proprietaire->donneIdentifiant()
    	);
    	
    	$this->assertEquals(count($O_proprietaire->donneChats()), $queryTable->getRowCount());
    }
    
    public function testSuppressionDeDeuxChats() {
    	// on travaille sur le proprietaire issu des fixtures
    	$O_proprietaire = self::$O_mapperDeTest->trouverParIdentifiant(1);
		// Par défaut, il a deux chats
    	$this->assertEquals(2, count($O_proprietaire->donneChats()));
    	
    	$O_chat = $O_proprietaire->donneChats()[0];
    	$O_proprietaire->enleveChat($O_chat);
    	
    	$O_chat = $O_proprietaire->donneChats()[1];
    	$O_proprietaire->enleveChat($O_chat);
    	
    	// On a enlevé les deux chats, il n'en reste plus !
    	$this->assertEquals(0, count($O_proprietaire->donneChats()));
    	 
    	// suppression physique
    	self::$O_mapperDeTest->actualiser($O_proprietaire);

    	// nos deux chats ont disparu en base
    	$queryTable = $this->getConnection()->createQueryTable(
    			'pc', 'SELECT * FROM proprietaire_chat where id_proprietaire = ' . $O_proprietaire->donneIdentifiant()
    	);

    	// on verifie que ce qui est dans l'objet correspond a ce qui est en base
    	$this->assertEquals(count($O_proprietaire->donneChats()), $queryTable->getRowCount());
    }
}