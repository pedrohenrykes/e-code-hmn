<?php

class AtendimentoDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_list_atendimento" );
        $this->form->setFormTitle( "Registro de Atendimento" );
        $this->form->class = "tform";

        $id                     = new THidden( "id" );
        $paciente_id            = new THidden( "paciente_id" );
        $bau_id                 = new THidden( "bau_id" );
        $profissional_id        = new THidden( "profissional_id" ); // Deve ser capturado a partir da sessão
        $paciente_nome          = new TEntry( "paciente_nome" );
        $dataclassificacao      = new TDate( "dataatendimento" );
        $horaclassificacao      = new TDateTime( "horaatendimento" );
        $exameclinico           = new TText( "exameclinico" );
        $examescomplementares   = new TText( "examescomplementares" );
        $diagnosticomedico      = new TText( "diagnosticomedico" );
        $descricaotratamento    = new TText( "descricaotratamento" );

        $paciente_nome->setSize("60%");
        $exameclinico->setSize("90%");
        $examescomplementares->setSize("90%");
        $diagnosticomedico->setSize("90%");
        $descricaotratamento->setSize("90%");
        $horaclassificacao->setSize("15%");
        $dataclassificacao->setSize("45%");

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bau_id->setValue ($fk);
        $paciente_id->setValue ($did);
        $profissional_id->setValue ($did);
        //$profissional_id  ALTERAR PARA O DA SESSSAAAAOOOOO

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


        $dataclassificacao->setMask( "dd/mm/yyyy" );
        $dataclassificacao->setDatabaseMask("yyyy-mm-dd");
        $horaclassificacao->setMask( "hh:ii" );

        $dataclassificacao->setValue( date( "d/m/Y" ) );
        $horaclassificacao->setValue( date( "H:i" ) );
        $horaclassificacao->setEditable( false );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $dataclassificacao->addValidation( TextFormat::set( "Data da Avaliação" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields([ new TLabel( "Data do Atendimento: {$redstar}" ) ], [ $dataclassificacao , $horaclassificacao ] );

        $this->form->addFields( [ new TLabel( "Exame Clinico:" ) ], [ $exameclinico ] );
        $this->form->addFields( [ new TLabel( "Exames Complementares:" ) ], [ $examescomplementares ] );
        $this->form->addFields( [ new TLabel( "Diagnóstico:" ) ], [ $diagnosticomedico ] );
        $this->form->addFields( [ new TLabel( "Descrição do Tratamento:" ) ], [ $descricaotratamento ] );
        $this->form->addFields( [ $id, $bau_id, $paciente_id, $profissional_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );

        $onReload = new TAction( [ "PacientesAtendimentoList", "onReload" ] );
        $onReload->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_1 = new TDataGridColumn( "paciente_nome", "Paciente", "left" );
        $column_2 = new TDataGridColumn( "enfermeiro_nome", "Responsável", "left" );
        $column_3 = new TDataGridColumn( "dataatendimento", "Data", "left" );
        $column_4 = new TDataGridColumn( "horaatendimento", "Hora", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );
        $this->datagrid->addColumn( $column_4 );

        $action_edit = new CustomDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $action_edit->setParameter( "did", $did );
        $this->datagrid->addAction( $action_edit );

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
        $container->style = "width: 90%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauAtendimentoRecord" );

        try {

            $this->form->validate();
            TTransaction::open( "database" );
            unset( $object->paciente_name );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "AtendimentoDetail", "onReload" ] );
            $action->setParameters( $param );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            $this->form->setData( $object );
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param = null )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {
                TTransaction::open( "database" );

                $object = new BauAtendimentoRecord( $param[ "key" ] );

                //$dataclassificacao = new DateTime( $object->dataclassificacao );
                //$object->dataclassificacao = $dataclassificacao->format("d/m/Y");

                $object->dataatendimento = TDate::date2br($object->dataatendimento);

                $horaclassificacao = new DateTime( $object->horaclassificacao );
                $object->horaclassificacao = $horaclassificacao->format("H:i");

                $this->onReload( $param );
                $this->form->setData( $object );
                TTransaction::close();

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

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
            $object = new BauAtendimentoRecord( $param[ "key" ] );
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

            $repository = new TRepository( "BauAtendimentoRecord" );

            $properties = [
                "order" => "dataatendimento",
                "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bau_id", "=", $param[ "fk" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    //$dataclassificacao = new DateTime( $object->dataclassificacao );

                    //$object->dataclassificacao = $dataclassificacao->format("d/m/Y");

                    $object->dataatendimento = TDate::date2br($object->dataatendimento);
                    $horaclassificacao = new DateTime( $object->horaclassificacao );
                    $object->horaclassificacao = $horaclassificacao->format("H:i");

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
