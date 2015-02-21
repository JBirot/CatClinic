<?php

class ChatMapperTest extends Generic_Tests_DatabaseTestCase
{
    protected static $O_connexionViaFramework;

    protected static $O_chatDeTest;

    protected static $O_chatMapper;
    
    protected static $S_cheminFixtures;

    public static function setUpBeforeClass()
    {
        self::$O_connexionViaFramework = Connexion::recupererInstance('test');
        self::$O_chatDeTest = new Chat();
        self::$O_chatDeTest->changeAge(new DateTime('1980-12-24'));
        self::$O_chatDeTest->changeNom('Patapon');
        self::$O_chatDeTest->changeTatouage('XXX111');

        self::$O_chatMapper = FabriqueDeMappers::fabriquer('chat', self::$O_connexionViaFramework);
        self::$S_cheminFixtures = dirname(__FILE__) . "/fixtures/chats/";
    }

    public static function tearDownAfterClass()
    {
        self::$O_connexionViaFramework = null;
    }

    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            self::$S_cheminFixtures . "chats.yml"
        );
    }

    public function testCreerChat() {
        
        $dataSet = $this->getConnection()->createDataSet();

        $this->assertEquals(2, $this->getConnection()->getRowCount(Constantes::TABLE_CHAT));

        self::$O_chatMapper->creer(self::$O_chatDeTest);

        $this->assertGreaterThan(0, self::$O_chatDeTest->donneIdentifiant());

        $queryTable = $this->getConnection()->createQueryTable(
            'chat', 'SELECT * FROM ' . Constantes::TABLE_CHAT
        );

        $expectedTable = $dataSet->getTable(Constantes::TABLE_CHAT);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testRechercheParTatouageInvalide() {
        $this->setExpectedException('Exception');
        $O_chat = self::$O_chatMapper->trouverParTatouage(self::$O_chatDeTest->donneTatouage());
    }
    
    public function testRechercheParTatouageValide(){
    	$O_chat = self::$O_chatMapper->trouverParTatouage('123ABCD');
    	$this->assertEquals('123ABCD',$O_chat->donneTatouage());
    }
    
    public function testSupprimerChat() {
    	self::$O_chatMapper->creer(self::$O_chatDeTest);
    	$O_chat = self::$O_chatMapper->trouverParTatouage(self::$O_chatDeTest->donneTatouage());
    	$this->assertEquals($O_chat->donneTatouage(), self::$O_chatDeTest->donneTatouage());
    	self::$O_chatMapper->supprimer(self::$O_chatDeTest);
    	$this->assertEquals(2, $this->getConnection()->getRowCount(Constantes::TABLE_CHAT));
    }

    public function testRechercheParIdentifiantInvalide() {
        $this->setExpectedException('Exception');
        $O_chat = self::$O_chatMapper->trouverParIdentifiant(-1);
    }
    
    public function testRechercheParIdentifiantValide(){
    	$O_chat = self::$O_chatMapper->trouverParIdentifiant(1);
    	$this->assertEquals(1,$O_proprietaire->donneIdentifiant());
    }
    
    public function testCreationMauvaisNom(){
		$this->setExpectedException('Exception');
		$O_chat = self::$O_chatDeTest;
		$O_chat->changeNom('555');
		self::$O_chatMapper->creer($O_chat);
    }
    
    public function testCreationMauvaisTatouage(){
    	$this->setExpectedException('Exception');
    	$O_chat = self::$O_chatDeTest;
    	$O_chat->changeTatouage('@ze-1');
    }
    
    public function testCreationMauvaiseDate(){
    	$this->setExpectedException('Exception');
    	$O_chat = self::$O_chatDeTest;
    	$O_chat->changeAge('Lalala');
    }
}