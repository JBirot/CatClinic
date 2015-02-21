<?php
class ProprietaireMapperTest extends Generic_Test_DatabaseTestCase
{
	protected static $O_connexionViaFramework;
	
	protected static $O_visiteDeTest;
	
	protected static $O_chatDeTest;
	
	protected static $O_praticienDeTest;
	
	protected static $O_visiteMapper;
	
	protected static $O_chatMapper;
	
	protected static $O_praticienMapper;
	
	protected static $S_cheminFixtures;
	
	public function setUp()
	{
		parent::setUp();
		
		self::$O_visiteDeTest = new Visite();
		self::$O_visiteDeTest->changeDate(new DateTime('1980-12-24'));
		self::$O_visiteDeTest->changePrix(11.52);
		
		self::$O_praticienDeTest = new Praticien();
		self::$O_praticienDeTest->changeNom('Phoque');
		self::$O_praticienDeTest->changePrenom('Bibi');
		
		self::$O_chatDeTest = new Chat();
        self::$O_chatDeTest->changeAge(new DateTime('1980-12-24'));
        self::$O_chatDeTest->changeNom('Patapon');
        self::$O_chatDeTest->changeTatouage('XXX000');
	}
	
	public static function setUpBeforeClass()
	{
		self::$O_connexionViaFramework = Connexion::recupererInstance('test');
		self::$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', self::$O_connexionViaFramework);
		self::$O_chatMapper = FabriqueDeMappers::fabriquer('chat', self::$O_connexionViaFramework);
		self::$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', self::$O_connexionViaFramework);
		self::$S_cheminFixtures = dirname(__FILE__) . "/fixtures/visites/";
	}
	
	public static function tearDownAfterClass()
	{
		self::$O_connexionViaFramework = null;
	}
	
	public function getDataSet()
	{
		$dataset = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(self::$S_cheminFixtures . "visites.yml");
		$dataset->addYamlFile(self::$S_cheminFixtures . "chats.yml");
		$dataset->addYamlFile(self::$S_cheminFixtures . "praticiens.yml");
		return $dataset;
	}
	
	public function testInsertionSimple(){
		self::$O_visiteDeTest->changeChat(self::$O_chatMapper->trouverParIdentifiant(1));
		self::$O_visiteDeTest->changePraticien(self::$O_praticienMapper->trouverParIdentifiant(1));
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);
		$this->assertGreaterThan(0,self::$O_visiteDeTest->donneIdentifiant());
	}
	
	public function testRecharcheParIdentifiantInvalide(){
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->trouverParIdentifiant(-1);
	}
	
	public function testRechercheParIdentifiantValide(){
		$O_visite = self::$O_visiteMapper->trouverParIdentifiant(1);
		$this->assertEquals(1,$O_visite->donneIdentifiant());
	}
	
	public function testRechercheParIdentifiantChatInvalide(){
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->trouverParIdentifiantChat(-1);
	}
	
	public function testRechercheParIdentifiantChatValide(){
		$O_visite = self::$O_visiteMapper->trouverParIdentifiantChat(1);
		$this->assertEquals(1,$O_visite->donneChat()->donneIdentifiant());
	}
	
	public function testInsertionEchoueCauseDate()
	{
		self::$O_visiteDeTest->changeChat(self::$O_chatMapper->trouverParIdentifiant(1));
		self::$O_visiteDeTest->changePraticien(self::$O_praticienMapper->trouverParIdentifiant(1));
		self::$O_visiteDeTest->changeDate('ABCD');
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);
	}
	
	public function testInsertionEchoueCausePrix()
	{
		self::$O_visiteDeTest->changeChat(self::$O_chatMapper->trouverParIdentifiant(1));
		self::$O_visiteDeTest->changePraticien(self::$O_praticienMapper->trouverParIdentifiant(1));
		self::$O_visiteMapper->changePrix('ABCD');
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);
	}
	
	public function testInsertionEchoueCauseChatVide()
	{
		self::$O_visiteDeTest->changePraticien(self::$O_praticienMapper->trouverParIdentifiant(1));
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);		
	}
	
	public function testInsertionEchoueCauseChatAbsentBDD()
	{
		self::$O_visiteDeTest->changeChat(self::$O_chatDeTest);
		self::$O_visiteDeTest->changePraticien(self::$O_praticienMapper->trouverParIdentifiant(1));
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);
	}
	
	public function testInsertionEchoueCausePraticienVide()
	{
		self::$O_visiteDeTest->changeChat(self::$O_chatMapper->trouverParIdentifiant(1));
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);		
	}
	
	public function testInsertionEchoueCausePraticienAbsentBDD()
	{
		self::$O_visiteDeTest->changeChat(self::$O_chatMapper->trouverParIdentifiant(1));
		self::$O_visiteDeTest->changePraticien(self::$O_praticienDeTest);
		$this->setExpectedException('Exception');
		self::$O_visiteMapper->creer(self::$O_visiteDeTest);
	}
	
	public function testSuppression(){
		$O_visite = self::$O_visiteMapper->trouverParIdentifiant(1);
    	self::$O_mapperDeTest->supprimer($O_proprietaire);
    	$this->assertEquals(0, $this->getConnection()->getRowCount(Constantes::TABLE_PROPRIETAIRE));
	}
}