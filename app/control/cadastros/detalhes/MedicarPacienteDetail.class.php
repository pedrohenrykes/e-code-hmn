<?php

class MedicarPacienteDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_list_medicar" );
        $this->form->setFormTitle( "Registro de Medicação" );
        $this->form->class = "tform";

        $id                     = new THidden( "id" );
        $bauprescricao_id       = new THidden( "bauprescricao_id" );
        $profissional_id        = new THidden( "profissional_id" );
        $medicamento_id         = new THidden( "medicamento_id" );
        $paciente_id            = new THidden( "paciente_id" );
        $data_aplicacao         = new TDate( "data_aplicacao" );
        $paciente_nome          = new TEntry( "paciente_nome" );
        $descricaotratamento    = new TText( "descricaotratamento" );

        $paciente_nome->setSize("60%");
        $data_aplicacao->setSize("45%");

        $id2 = filter_input( INPUT_GET, "key" );
        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bauprescricao_id->setValue ($id2);
        $medicamento_id->setValue ($fk);
        $paciente_id->setValue ($did);

        try {
            TTransaction::open( "database" );
            $paciente = new PacienteRecord($did);
            if( isset($paciente) ) {
                $paciente_nome->setValue($paciente->nomepaciente);
            }
            TTransaction::close();
        } catch (Exception $ex) {
            $action = new TAction(["PacientesEncaminhamentoList", "onReload"]);
            new TMessage("error", "Ocorreu um erro ao carregar as dependência do formulário.", $action);
        }

        $data_aplicacao->setMask( "dd/mm/yyyy h:i:s" );
        $data_aplicacao->setDatabaseMask("yyyy-mm-dd h:i:s");

        $data_aplicacao->setValue( date( "d/m/Y h:i:s" ) );
        $data_aplicacao->setEditable( false );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data: {$redstar}" ) ], [ $data_aplicacao ] );
        $this->form->addFields( [ $id, $paciente_id, $medicamento_id, $profissional_id, $bauprescricao_id ] );

        $onReload = new TAction( [ "PacienteMedicacaoList", "onReload" ] );
        $onReload->setParameter( "did", $did );

        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( '100%' );

        $column_1 = new TDataGridColumn( "medicamento_nome", "Medicação", "left" );
        $column_2 = new TDataGridColumn( "dosagem", "Dose", "left" );
        $column_3 = new TDataGridColumn( "posologia", "Posologia", "left" );
        $column_4 = new TDataGridColumn( "data_prescricao", "Data da Prescrição", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );
        $this->datagrid->addColumn( $column_4 );

        $action_avaliacao = new CustomDataGridAction( [ $this , "onSave" ] );
        $action_avaliacao->setButtonClass( "btn btn-primary" );
        $action_avaliacao->setImage( "fa:user-md white fa-lg" );
        $action_avaliacao->setField( "id" );
        $action_avaliacao->setFk( "medicamento_id" );
        $action_avaliacao->setDid( "paciente_id" );
        $action_avaliacao->setUseButton(TRUE);
        $this->datagrid->addQuickAction( "Confirmar AAplicação", $action_avaliacao, 'id');

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

    public function onSave( $param )
    {
        $object = $this->form->getData( "BauAplicacaoRecord" );

        try {
            $object->profissional_id = TSession::getValue('profissionalid');
            $object->paciente_id = $param['did'];
            $object->medicamento_id = $param['fk'];
            $object->bauprescricao_id = $param['key'];

            //$object->status = 'APlICADO';
            unset( $object->paciente_nome );

            $this->form->validate();
            TTransaction::open( "database" );

            $object->store();
            TTransaction::close();

            $action = new TAction( [ $this, "onReload" ] );
            $action->setParameters( $param );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            $this->form->setData( $object );
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    

    public function onReload( $param )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "BauPrescricaoRecord" );

            $properties = [
            "order" => "data_prescricao",
            "direction" => "desc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            //$criteria->add( new TFilter( "bau_id", "=", $param[ "fk" ] ) );
            //$criteria->add( new TFilter( "status", "=", 'PRESCRITO' ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $object->data_prescricao = TDate::date2br($object->data_prescricao) . ' ' . substr($object->data_prescricao, 11, strlen($object->data_prescricao));

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

    public function onClear()
    {
        $this->form->clear();
    }

    
}
