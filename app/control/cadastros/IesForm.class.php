<?php

class IesForm extends TWindow
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Formulário de Instituição de Ensino Superior" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder("form_ies" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id       = new THidden( "id" );
        $nomeies  = new TEntry( "nomeies" );
        $endereco = new TEntry( "endereco" );
        $bairro   = new TEntry( "bairro" );
        $cidade   = new TEntry( "cidade" );
        $cep      = new TEntry( "cep" );
        $uf       = new TDBCombo( "uf", "database", "EstadosRecord", "sigla", "estado", "estado" );

        $id->setSize( '38%' );
        $nomeies->setSize( '38%' );
        $endereco->setSize('38%');
        $bairro->setSize( '38%' );
        $cidade->setSize( '38%' );
        $cep->setSize( '38%' );
        $uf->setSize('38%');

        $cep->setProperty( 'placeholder', "Ex.: (84)99999-8888" );

        $cep->setMask('99999-999');

        $uf->setDefaultOption( '..::SELECIONE::..' );

        $label01 = new RequiredTextFormat( [ "Nome", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Cidade", "#F00", "bold" ] );
        $label03 = new RequiredTextFormat( [ "UF", "#F00", "bold" ] );

        $nomeies->addValidation( $label01->getText(), new TRequiredValidator);
        $cidade->addValidation( $label02->getText(), new TRequiredValidator);
        $uf->addValidation( $label03->getText(), new TRequiredValidator);

        $this->form->addFields( [ new TLabel( "Nome: $redstar" ) ], [ $nomeies ] );
        $this->form->addFields( [ new TLabel( "Cidade: $redstar" ) ], [ $cidade ] );
        $this->form->addFields( [ new TLabel( "UF: $redstar" ) ], [ $uf ] );
        $this->form->addFields( [ new TLabel( "Endereço" )], [ $endereco ] );
        $this->form->addFields( [ new TLabel( "Bairro" )], [ $bairro ] );
        $this->form->addFields( [ new TLabel( "CEP" )], [ $cep ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( 'Salvar', new TAction( [ $this, 'onSave' ] ), 'fa:save' );

        $container = new TVBox();
        $container->style = "width: 100%";
        // $container->add( new TXMLBreadCrumb( "menu.xml", "IesList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( 'database' );

            $object = $this->form->getData( 'IesRecord' );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ 'IesList', 'onReload' ] );

            new TMessage( 'info', 'Registro salvo com sucesso!', $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>' . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ 'key' ] ) ) {

                TTransaction::open( 'database' );

                $object = new IesRecord( $param[ 'key' ] );

                TTransaction::close();

                $this->form->setData( $object );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar carregar o registro para edição!<br><br>' . $ex->getMessage() );
        }
    }
}
