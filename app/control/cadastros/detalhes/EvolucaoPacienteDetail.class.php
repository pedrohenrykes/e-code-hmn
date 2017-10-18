<?php

class EvolucaoPacienteDetail extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Evolução do Paciente" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_solicitar_exame" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                 = new THidden("id");
        $bau_id             = new THidden("bau_id");
        $paciente_nome      = new TEntry( "paciente_nome" );
        $situacao           = new TCombo( "situacao" );
        $profissional_id    = new THidden("profissional_id");
        $datasolicitacao     = new TDateTime("data_evolucao");
        $observacao         = new TText("observacao");

        $situacao->setDefaultOption( "..::SELECIONE::.." );
        $situacao->addItems( [ 
            "ENCAMINHAR" => "ENCAMINHAR",
            "INTERNAMENTO" => "INTERNAMENTO",
            "REMOCAO" => "REMOÇÃO",
            "ALTA MÉDICA" => "ALTA MÉDICA"
        ] );
        

        $id2 = filter_input( INPUT_GET, "key" );
        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bau_id->setValue ($did);

        try {

            TTransaction::open( "database" );

            $paciente = new BauRecord( $did );

            if( isset( $paciente ) ) {
                $paciente_nome->setValue( $paciente->paciente_nome );

            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            $action = new TAction( [ "PacientesAtendimentoList", "onReload" ] );
            new TMessage( "error", "Ocorreu um erro ao carregar as dependência do formulário.", $action );

        }

        $paciente_nome      ->setSize( "70%" );
        $datasolicitacao    ->setSize( "20%" );
        $situacao           ->setSize( "70%" );
        $observacao         ->setSize( "70%" );
    
        $datasolicitacao    ->setMask( "dd/mm/yyyy" );
        $datasolicitacao    ->setDatabaseMask("yyyy-mm-dd");
        $datasolicitacao    ->setValue( date( "d/m/Y" ) );

        $datasolicitacao    ->setEditable( false );
        $paciente_nome      ->setEditable( false );

        //$principioativo_id->addValidation( TextFormat::set( "Medicamento" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente:" ) ], [ $paciente_nome, $datasolicitacao ] );
        $this->form->addFields( [ new TLabel( "Situação" ) ], [ $situacao ] );
        $this->form->addFields( [ new TLabel( "Observação" ) ], [ $observacao ] );
        $this->form->addFields( [ $id, $profissional_id, $bau_id ] );

        $onSave   = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( '100%' );

        $column_1 = new TDataGridColumn( "situacao", "Situação", "left" );
        $column_5 = new TDataGridColumn( "data_evolucao", "Data", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_5 );
        
        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $action_del->setParameter( "did", $did );
        $this->datagrid->addAction( $action_del );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload"] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();

        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave( $param = null ){

        try {

            $this->form->validate();
            $object = $this->form->getData( "BauEvolucaoRecord" );
            $object->profissional_id = TSession::getValue('profissionalid');

            TTransaction::open( "database" );

            unset($object->paciente_nome);
            $object->store();

            TTransaction::close();

            $action = new TAction( [ $this, "onReload" ] );
            $action->setParameters( $param );
            new TMessage( "info", "Registro gravado com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }
   

    public function onDelete( $param = null )
    {
        if( isset( $param[ "key" ] ) ) {

            $param = [
            "key" => $param[ "key" ],
            "fk"  => $param[ "fk" ],
            "did"  => $param[ "did" ]
            ];

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameters( $param );
            $action2->setParameters( $param );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    public function Delete( $param = null )
    {
        try {

            TTransaction::open( "database" );
            $object = new BauEvolucaoRecord( $param[ "key" ] );
            $object->delete();
            TTransaction::close();

            $this->onReload( $param );

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReload( $param )
    {
        try {

            TTransaction::open( "database" );
            $repository = new TRepository( "BauEvolucaoRecord" );
            $properties = [ "order" => "data_evolucao", "direction" => "desc" ];
            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bau_id", "=", $param[ "did" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $object->data_evolucao = TDate::date2br($object->data_evolucao);
                    $this->datagrid->addItem( $object );

                }

            }

            $criteria->resetProperties();
            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
            $this->pageNavigation->setLimit( $limit );

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

}
