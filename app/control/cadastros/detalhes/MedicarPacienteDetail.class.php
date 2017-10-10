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
        $enfermeiro_id        = new THidden( "enfermeiro_id" );
        $data_aplicacao2         = new TDate( "data_aplicacao2" );
        $paciente_nome          = new TEntry( "paciente_nome" );

        $paciente_nome->setSize("60%");
        $data_aplicacao2->setSize("45%");

        $id2 = filter_input( INPUT_GET, "key" );
        $fk = filter_input( INPUT_GET, "key" );
        $did = filter_input( INPUT_GET, "did" );

        $bauprescricao_id->setValue ($id2);

        try {
            TTransaction::open( "database" );
            $paciente = new BauAtendimentoRecord($did);
            if( isset($paciente) ) {
                $paciente_nome->setValue($paciente->paciente_nome);
            }
            TTransaction::close();
        } catch (Exception $ex) {
            $action = new TAction(["PacienteMedicacaoList", "onReload"]);
            new TMessage("error", "Ocorreu um erro ao carregar as dependência do formulário.", $action);
        }

        $data_aplicacao2->setMask( "dd/mm/yyyy h:i:s" );
        $data_aplicacao2->setDatabaseMask("yyyy-mm-dd h:i:s");

        $data_aplicacao2->setValue( date( "d/m/Y h:i:s" ) );
        $data_aplicacao2->setEditable( false );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data: {$redstar}" ) ], [ $data_aplicacao2 ] );
        $this->form->addFields( [ $id, $enfermeiro_id, $bauprescricao_id ] );

        $onReload = new TAction( [ "PacienteMedicacaoList", "onReload" ] );
        $onReload->setParameter( "did", $did );

        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( '100%' );

        $column_1 = new TDataGridColumn( "medicamento_nome", "Medicação", "left" );
        $column_2 = new TDataGridColumn( "dosagem", "Dose", "left" );
        $column_4 = new TDataGridColumn( "data_prescricao", "Data da Prescrição", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_4 );
        
        $action_avaliacao = new CustomDataGridAction( [ $this , "onSave" ] );
        $action_avaliacao->setButtonClass( "btn btn-primary" );
        $action_avaliacao->setImage( "fa:user-md white fa-lg" );

        $action_avaliacao->setParameter('id', '' . filter_input(INPUT_GET, 'id') . '');
        $action_avaliacao->setParameter('fk', '' . filter_input(INPUT_GET, 'did') . '');
        $action_avaliacao->setParameter('did', '' . filter_input(INPUT_GET, 'did') . '');

        /*
        $action_avaliacao->setField( "key" );
        $action_avaliacao->setFk( "fk" );
        $action_avaliacao->setDid( "did" );
        */
        
        
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

    public function onSave( $param ){
        try {
            if (isset($param['key'])) {

                $key = $param['key'];
                $fk = $param['fk'];
                $did = $param['did'];
                TTransaction::open('database');

                $object = new MedicarRecord($key);

                $object->enfermeiro_id = TSession::getValue('profissionalid');
                $object->status = 'APLICADO';
                $object->data_aplicacao =  date("Y/m/d h:i:s");


                $object->store();

                $action = new TAction( [ $this, "onReload" ] );
                $action->setParameters( $param );

                new TMessage( "info", "Registro salvo com sucesso!", $action );

                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onReload( $param )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "MedicarRecord" );

            $properties = [
            "order" => "id",
            "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bauatendimento_id", "=", $param[ "did" ] ) );
            $criteria->add( new TFilter( "status", "=", 'PRESCRITO' ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    //$object->data_prescricao = TDate::date2br($object->data_prescricao) . ' ' . substr($object->data_prescricao, 11, strlen($object->data_prescricao));

                    $object->data_prescricao = TDate::date2br($object->data_prescricao);

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
