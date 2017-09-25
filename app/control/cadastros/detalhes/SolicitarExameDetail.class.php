<?php

class SolicitarExameDetail extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Solicitar Exames" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_solicitar_exame" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                 = new THidden("id");
        $bau_id             = new THidden("bau_id");
        $paciente_id        = new THidden( "paciente_id" );
        $exame_id           = new THidden( "exame_id" );
        $paciente_nome      = new TEntry( "paciente_nome" );
        $profissional_id    = new THidden("profissional_id");
        $datasolicitacao     = new TDateTime("datasolicitacao");
        $observacao         = new TText("observacao");

        $criteria3 = new TCriteria;
        $exame = new TDBMultiSearch('exame', 'database', 'VwTipoExameNomeRecord', 'exame_id', 'tipo_exame', 'tipo_exame', $criteria3);

        $exame->style = "text-transform: uppercase;";
        $exame->setProperty('placeholder', 'DIGITE O EXAME OU TIPO DO EXAME');
        $exame->setMinLength(1);
        $exame->setMaxSize(1);

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bau_id->setValue ($fk);
        $paciente_id->setValue ($did);

        try {

            TTransaction::open( "database" );

            $bau = new BauRecord( $fk );
            $paciente = new PacienteRecord( $did );

            if( isset( $bau ) && isset( $paciente ) ) {
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );

            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            $action = new TAction( [ "PacientesAtendimentoList", "onReload" ] );
            new TMessage( "error", "Ocorreu um erro ao carregar as dependência do formulário.", $action );

        }

        $exame              ->setSize( "70%" );
        $paciente_nome      ->setSize( "70%" );
        $datasolicitacao    ->setSize( "20%" );
        $observacao         ->setSize( "70%" );
    
        $datasolicitacao    ->setMask( "dd/mm/yyyy" );
        $datasolicitacao    ->setDatabaseMask("yyyy-mm-dd");
        $datasolicitacao    ->setValue( date( "d/m/Y" ) );

        $datasolicitacao    ->setEditable( false );
        $paciente_nome      ->setEditable( false );

        //$principioativo_id->addValidation( TextFormat::set( "Medicamento" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente:" ) ], [ $paciente_nome, $datasolicitacao ] );
        $this->form->addFields( [ new TLabel( "Exame:{$redstar}" ) ], [ $exame ] );
        $this->form->addFields( [ new TLabel( "Observação" ) ], [ $observacao ] );
        $this->form->addFields( [ $id, $paciente_id, $profissional_id, $bau_id, $exame_id ] );

        $onSave   = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );
        $onReload = new TAction( [ "AtendimentoDetail", "onReload" ] );
        $onReload->setParameter( "fk", $fk );
        $onReload->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar para Atendimento", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( '100%' );

        $column_1 = new TDataGridColumn( "exame_nome", "Exame", "left" );
        $column_5 = new TDataGridColumn( "datasolicitacao", "Data da solicitação", "left" );

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
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
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
            $object = $this->form->getData( "BauExameRecord" );
            $object->exame_id = key($object->exame);
            $object->profissional_id = TSession::getValue('profissionalid');

            TTransaction::open( "database" );

            unset($object->paciente_nome);
            unset($object->exame);
            $object->store();

            TTransaction::close();

            $action = new TAction( [ $this, "onReload" ] );
            $action->setParameters( $param );
            new TMessage( "info", "Exame Solicitado com sucesso!", $action );

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
            $object = new BauExameRecord( $param[ "key" ] );
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
            $repository = new TRepository( "BauExameRecord" );
            $properties = [ "order" => "datasolicitacao", "direction" => "desc" ];
            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bau_id", "=", $param[ "fk" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $object->datasolicitacao = TDate::date2br($object->datasolicitacao);
                    $this->datagrid->addItem( $object );

                }

            }

            $criteria->resetProperties();
            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
            $this->pageNavigation->setLimit( $limit );

            //$this->onReloadFrames( $param );

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

}
