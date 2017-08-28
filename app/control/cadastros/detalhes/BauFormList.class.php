<?php

class BauFormList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $fk = filter_input( INPUT_GET, "fk" );

        $this->form = new BootstrapFormBuilder( "form_list_bau" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                       = new THidden("id");
        $paciente_id              = new THidden( "paciente_id" );
        $paciente_nome            = new TEntry( "paciente_nome" );
        $especificartransporte    = new TEntry("especificartransporte");
        $responsavel              = new TEntry("responsavel");
        $queixaprincipal          = new TText("queixaprincipal");
        $dataentrada              = new TDate("dataentrada");
        $dataaltahospitalar       = new TDate("dataaltahospitalar");
        $dataobito                = new TDate("dataobito");
        $declaracaoobitodata      = new TDate("declaracaoobitodata");
        $datainternamento         = new TDate("datainternamento");
        $dataremocao              = new TDate("dataremocao");
        $datatransferencia        = new TDate("datatransferencia");
        $datatransporte           = new TDate("datatransporte");
        $horaentrada              = new TDateTime("horaentrada");
        $horaaltahospitalar       = new TDateTime("horaaltahospitalar");
        $horaobito                = new TDateTime("horaobito");
        $declaracaoobitohora      = new TDateTime("declaracaoobitohora");
        $internamentolocal        = new TCombo("internamentolocal");
        $remocao                  = new TCombo("remocao");
        $transferencia            = new TCombo("transferencia");
        $alta                     = new TCombo("alta");
        $obito                    = new TCombo("obito");

        $localremocao_id          = new TCombo("localremocao_id");
        $localtransferencia_id    = new TCombo("localtransferencia_id");
        $transportedestino_id     = new TCombo("transportedestino_id");
        $tipoaltahospitalar_id    = new TCombo("tipoaltahospitalar_id");
        $medicoalta_id            = new TCombo("medicoalta_id");
        $destinoobito_id          = new TCombo("destinoobito_id");
        $declaracaoobitomedico_id = new TCombo("declaracaoobitomedico_id");
        $convenio_id              = new TDBCombo( "convenio_id", "database", "ConvenioRecord", "id", "nome", "nome");

        try {

            TTransaction::open( "database" );

            $paciente = new PacienteRecord( $fk );

            if( isset( $paciente ) ){
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );
            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            new TMessage( "error", "Não foi possível carregar os dados do paciente.<br><br>" . $ex->getMessage() );

        }

        $id                      ->setSize( "38%" );
        $paciente_nome           ->setSize( "38%" );
        $especificartransporte   ->setSize( "38%" );
        $queixaprincipal         ->setSize( "38%" );
        $dataentrada             ->setSize( "38%" );
        $horaentrada             ->setSize( "38%" );
        $dataaltahospitalar      ->setSize( "38%" );
        $horaaltahospitalar      ->setSize( "38%" );
        $dataobito               ->setSize( "38%" );
        $horaobito               ->setSize( "38%" );
        $declaracaoobitodata     ->setSize( "38%" );
        $declaracaoobitohora     ->setSize( "38%" );
        $datainternamento        ->setSize( "38%" );
        $dataremocao             ->setSize( "38%" );
        $datatransferencia       ->setSize( "38%" );
        $datatransporte          ->setSize( "38%" );
        $remocao                 ->setSize( "38%" );
        $transferencia           ->setSize( "38%" );
        $internamentolocal       ->setSize( "38%" );
        $alta                    ->setSize( "38%" );
        $obito                   ->setSize( "38%" );
        $localremocao_id         ->setSize( "38%" );
        $localtransferencia_id   ->setSize( "38%" );
        $transportedestino_id    ->setSize( "38%" );
        $tipoaltahospitalar_id   ->setSize( "38%" );
        $medicoalta_id           ->setSize( "38%" );
        $destinoobito_id         ->setSize( "38%" );
        $declaracaoobitomedico_id->setSize( "38%" );
        $convenio_id             ->setSize( "38%" );
        $responsavel             ->setSize( "38%" );

        $remocao                 ->setDefaultOption( "..::SELECIONE::.." );
        $transferencia           ->setDefaultOption( "..::SELECIONE::.." );
        $internamentolocal       ->setDefaultOption( "..::SELECIONE::.." );
        $alta                    ->setDefaultOption( "..::SELECIONE::.." );
        $obito                   ->setDefaultOption( "..::SELECIONE::.." );
        $localremocao_id         ->setDefaultOption( "..::SELECIONE::.." );
        $localtransferencia_id   ->setDefaultOption( "..::SELECIONE::.." );
        $transportedestino_id    ->setDefaultOption( "..::SELECIONE::.." );
        $tipoaltahospitalar_id   ->setDefaultOption( "..::SELECIONE::.." );
        $medicoalta_id           ->setDefaultOption( "..::SELECIONE::.." );
        $destinoobito_id         ->setDefaultOption( "..::SELECIONE::.." );
        $declaracaoobitomedico_id->setDefaultOption( "..::SELECIONE::.." );
        $convenio_id             ->setDefaultOption( "..::SELECIONE::.." );

        $internamentolocal->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $remocao          ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $transferencia    ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $alta             ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $obito            ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );

        $internamentolocal->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $remocao          ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $transferencia    ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $alta             ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $obito            ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );

        $internamentolocal->setValue( "N" );
        $remocao          ->setValue( "N" );
        $transferencia    ->setValue( "N" );
        $alta             ->setValue( "N" );
        $obito            ->setValue( "N" );
        $convenio_id      ->setValue( "5" );


        $dataentrada        ->setMask( "dd/mm/yyyy" );
        $dataentrada        ->setDatabaseMask("yyyy-mm-dd");
        $datainternamento   ->setMask( "dd/mm/yyyy" );
        $datainternamento   ->setDatabaseMask("yyyy-mm-dd");
        $dataremocao        ->setMask( "dd/mm/yyyy" );
        $dataremocao        ->setDatabaseMask("yyyy-mm-dd");
        $datatransferencia  ->setMask( "dd/mm/yyyy" );
        $datatransferencia  ->setDatabaseMask("yyyy-mm-dd");
        $datatransporte     ->setMask( "dd/mm/yyyy" );
        $datatransporte     ->setDatabaseMask("yyyy-mm-dd");
        $dataaltahospitalar ->setMask( "dd/mm/yyyy" );
        $dataaltahospitalar ->setDatabaseMask("yyyy-mm-dd");
        $dataobito          ->setMask( "dd/mm/yyyy" );
        $dataobito          ->setDatabaseMask("yyyy-mm-dd");
        $declaracaoobitodata->setMask( "dd/mm/yyyy" );
        $declaracaoobitodata->setDatabaseMask("yyyy-mm-dd");
        $horaentrada        ->setMask( "hh:ii" );
        $horaaltahospitalar ->setMask( "hh:ii" );
        $horaobito          ->setMask( "hh:ii" );
        $declaracaoobitohora->setMask( "hh:ii" );

        $dataentrada        ->setValue( date( "d/m/Y" ) );
        $datainternamento   ->setValue( date( "d/m/Y" ) );
        $dataremocao        ->setValue( date( "d/m/Y" ) );
        $datatransferencia  ->setValue( date( "d/m/Y" ) );
        $datatransporte     ->setValue( date( "d/m/Y" ) );
        $dataaltahospitalar ->setValue( date( "d/m/Y" ) );
        $dataobito          ->setValue( date( "d/m/Y" ) );
        $declaracaoobitodata->setValue( date( "d/m/Y" ) );
        $horaentrada        ->setValue( date( "H:i" ) );
        $horaaltahospitalar ->setValue( date( "H:i" ) );
        $horaobito          ->setValue( date( "H:i" ) );
        $declaracaoobitohora->setValue( date( "H:i" ) );

        $paciente_nome->setEditable( false );

        $responsavel->forceUpperCase();
        $responsavel->setProperty( "title", "Caso o paciente seja menor de idade." );

        $label01 = new RequiredTextFormat( [ "Nome do Paciente", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Sexo", "#F00", "bold" ] );

        $convenio_id->addValidation( $label01->getText(), new TRequiredValidator );
        $paciente_id->addValidation( $label01->getText(), new TRequiredValidator );

        $page1 = new TLabel( "Paciente", "#7D78B6", 12, "bi");
        $page1->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Identificação" );
        $this->form->addContent( [ $page1 ] );
        $this->form->addFields( [ new TLabel( "Nome do Paciente: {$redstar}") ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Responsável:") ], [ $responsavel ] );
        $this->form->addFields( [ new TLabel( "Convênio:" ) ], [ $convenio_id ] );
        $this->form->addFields( [ new TLabel( "Data de Entrada: {$redstar}" ) ], [ $dataentrada ] );
        $this->form->addFields( [ new TLabel( "Hora de Entrada:" ) ], [ $horaentrada ] );
        $this->form->addFields( [ new TLabel( "Queixa Principal:" ) ], [ $queixaprincipal ] );
        $this->form->addFields( [ $id, $paciente_id ] );

        $page2 = new TLabel( "Estado Geral", "#7D78B6", 12, "bi" );
        $page2->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Avaliação" );
        $this->form->addContent( [ $page2 ] );
        // TODO rever a questão de relacionamento com a tabela de classificação de risco

        $page3 = new TLabel( "Encaminhamento", "#7D78B6", 12, "bi" );
        $page3->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Destinação" );
        $this->form->addContent( [ $page3 ] );
        $this->form->addFields( [ new TLabel( "Internamento: {$redstar}" ) ], [ $internamentolocal ]);
        $this->form->addFields( [ new TLabel( "Data de Internamento: {$redstar}" ) ], [ $datainternamento ] );

        // $page4 = new TLabel( "Remoção", "#7D78B6", 12, "bi" );
        // $page4->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        // $this->form->addContent( [ $page4 ] );
        $this->form->addFields( [ new TLabel( "Remoção: {$redstar}") ], [ $remocao ] );
        $this->form->addFields( [ new TLabel( "Data de Remoção: {$redstar}") ], [ $dataremocao ] );
        $this->form->addFields( [ new TLabel( "Local de Remoção: {$redstar}") ], [ $localremocao_id ] );

        // $page5 = new TLabel( "Transferência", "#7D78B6", 12, "bi");
        // $page5->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        // $this->form->addContent( [ $page5 ] );
        $this->form->addFields( [ new TLabel( "Transferência: {$redstar}") ], [ $transferencia ] );
        $this->form->addFields( [ new TLabel( "Data de Transferência: {$redstar}" ) ], [ $datatransferencia ] );
        $this->form->addFields( [ new TLabel( "Local de Transferência:" ) ], [ $localtransferencia_id ] );

        // $page6 = new TLabel( "Transporte", "#7D78B6", 12, "bi");
        // $page6->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        // $this->form->addContent( [ $page6 ] );
        // $this->form->addFields( [ new TLabel( "Destino do Transporte:" ) ], [ $transportedestino_id ] );
        // $this->form->addFields( [ new TLabel( "Informações do Transporte:" ) ], [ $especificartransporte ] );
        // $this->form->addFields( [ new TLabel( "Data do Transporte:" ) ], [ $datatransporte ] );

        $page7 = new TLabel( "Alta Hospitalar", "#7D78B6", 12, "bi");
        $page7->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Alta" );
        $this->form->addContent( [ $page7 ] );
        $this->form->addFields( [ new TLabel( "Alta:" ) ], [ $alta ] );
        $this->form->addFields( [ new TLabel( "Tipo de Alta:" ) ], [ $tipoaltahospitalar_id ] );
        $this->form->addFields( [ new TLabel( "Data da Alta:" ) ], [ $dataaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "Hora da Alta:" ) ], [ $horaaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "Médico Responsável:" ) ], [ $medicoalta_id ] );

        $page8 = new TLabel( "Declaração de Óbito", "#7D78B6", 12, "bi");
        $page8->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Óbito" );
        $this->form->addContent( [ $page8 ] );
        $this->form->addFields( [ new TLabel( "Óbito:" ) ], [ $obito ] );
        $this->form->addFields( [ new TLabel( "Data do Óbito:" ) ], [ $dataobito ] );
        $this->form->addFields( [ new TLabel( "Hora do Óbito:" ) ], [ $horaobito ] );
        $this->form->addFields( [ new TLabel( "Data da Declaração:" ) ], [ $declaracaoobitodata ] );
        $this->form->addFields( [ new TLabel( "Hora da Declaração:" ) ], [ $declaracaoobitohora ] );
        $this->form->addFields( [ new TLabel( "Destino do Corpo:" ) ], [ $destinoobito_id ] );
        $this->form->addFields( [ new TLabel( "Medico Responsável:" ) ], [ $declaracaoobitomedico_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "PacienteList", "onReload" ] ), "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome = new TDataGridColumn( "paciente_nome", "Nome", "left" );
        $column_dataentrada = new TDataGridColumn( "dataentrada", "Dia", "left" );
        $column_horaentrada = new TDataGridColumn( "horaentrada", "Hora", "center" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );

        $action_edit = new TDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_edit );

        $action_del = new TDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
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
        $object = $this->form->getData( "BauRecord" );

        try {

            $this->form->validate();

            TTransaction::open( "database" );

            unset( $object->alta );
            unset( $object->obito );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "BauFormList", "onReload" ] );
            $action->setParameter( "fk", $param[ "fk" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $object );

            $fields = [ "internamentolocal", "remocao", "transferencia", "alta", "obito" ];

            foreach ( $fields as $field ) {
                self::onChangeAction([
                    "_field_name" => $field,
                    $field => $object->$field
                ]);
            }

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param = null )
    {
        try {

            $fields = [ "internamentolocal", "remocao", "transferencia", "alta", "obito" ];

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new BauRecord( $param[ "key" ] );

                $dataobito           = new DateTime( $object->dataobito );
                $horaobito           = new DateTime( $object->horaobito );
                $dataentrada         = new DateTime( $object->dataentrada );
                $horaentrada         = new DateTime( $object->horaentrada );
                $dataremocao         = new DateTime( $object->dataremocao );
                $datainternamento    = new DateTime( $object->datainternamento );
                $datatransferencia   = new DateTime( $object->datatransferencia );
                $dataaltahospitalar  = new DateTime( $object->dataaltahospitalar );
                $horaaltahospitalar  = new DateTime( $object->horaaltahospitalar );
                $declaracaoobitodata = new DateTime( $object->declaracaoobitodata );
                $declaracaoobitohora = new DateTime( $object->declaracaoobitohora );

                $object->dataobito           = $dataobito->format("d/m/Y");
                $object->horaobito           = $horaobito->format("H:i");
                $object->dataentrada         = $dataentrada->format("d/m/Y");
                $object->horaentrada         = $horaentrada->format("H:i");
                $object->dataremocao         = $dataremocao->format("d/m/Y");
                $object->datainternamento    = $datainternamento->format("d/m/Y");
                $object->datatransferencia   = $datatransferencia->format("d/m/Y");
                $object->dataaltahospitalar  = $dataaltahospitalar->format("d/m/Y");
                $object->horaaltahospitalar  = $horaaltahospitalar->format("H:i");
                $object->declaracaoobitodata = $declaracaoobitodata->format("d/m/Y");
                $object->declaracaoobitohora = $declaracaoobitohora->format("H:i");

                $this->onReload( $param );

                $this->form->setData( $object );

                foreach ( $fields as $field ) {
                    self::onChangeAction([
                        "_field_name" => $field,
                        $field => $object->$field
                    ]);
                }

            } else {

                foreach ( $fields as $field ) {
                    self::onChangeAction([
                        "_field_name" => $field,
                        $field => "N"
                    ]);
                }

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }

    public function onReload( $param = null )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "BauRecord" );

            $properties = [
                "order" => "dataentrada",
                "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "paciente_id", "=", $param[ "fk" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();

            if ( !empty( $objects ) ) {

                foreach ( $objects as $object ) {

                    $dataobito           = new DateTime( $object->dataobito );
                    $horaobito           = new DateTime( $object->horaobito );
                    $dataentrada         = new DateTime( $object->dataentrada );
                    $horaentrada         = new DateTime( $object->horaentrada );
                    $dataremocao         = new DateTime( $object->dataremocao );
                    $datainternamento    = new DateTime( $object->datainternamento );
                    $datatransferencia   = new DateTime( $object->datatransferencia );
                    $dataaltahospitalar  = new DateTime( $object->dataaltahospitalar );
                    $horaaltahospitalar  = new DateTime( $object->horaaltahospitalar );
                    $declaracaoobitodata = new DateTime( $object->declaracaoobitodata );
                    $declaracaoobitohora = new DateTime( $object->declaracaoobitohora );

                    $object->dataobito           = $dataobito->format("d/m/Y");
                    $object->horaobito           = $horaobito->format("H:i");
                    $object->dataentrada         = $dataentrada->format("d/m/Y");
                    $object->horaentrada         = $horaentrada->format("H:i");
                    $object->dataremocao         = $dataremocao->format("d/m/Y");
                    $object->datainternamento    = $datainternamento->format("d/m/Y");
                    $object->datatransferencia   = $datatransferencia->format("d/m/Y");
                    $object->dataaltahospitalar  = $dataaltahospitalar->format("d/m/Y");
                    $object->horaaltahospitalar  = $horaaltahospitalar->format("H:i");
                    $object->declaracaoobitodata = $declaracaoobitodata->format("d/m/Y");
                    $object->declaracaoobitohora = $declaracaoobitohora->format("H:i");

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

    public function onDelete( $param = null )
    {
        if( isset( $param[ "key" ] ) ) {

            $param = [
                "key" => $param[ "key" ],
                "fk"  => $param[ "fk" ]
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

            $object = new BauRecord( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "erro", $ex->getMessage() );

        }
    }

    public static function onChangeAction( $param = null )
    {
        $object = new StdClass;

        $fieldName = $param[ "_field_name" ];

        switch ( $fieldName ) {

            case "internamentolocal":

                if( $param[ $fieldName ] == "S" ) {
                    TQuickForm::showField( "form_list_bau", "datainternamento" );
                } else {
                    $object->datainternamento = "";
                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "datainternamento" );
                }

                break;

            case "remocao":

                if( $param[ $fieldName ] == "S" ) {
                    TQuickForm::showField( "form_list_bau", "dataremocao" );
                    TQuickForm::showField( "form_list_bau", "localremocao_id" );
                } else {
                    $object->dataremocao = "";
                    $object->localremocao_id = "..::SELECIONE::..";
                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "dataremocao" );
                    TQuickForm::hideField( "form_list_bau", "localremocao_id" );
                }

                break;

            case "transferencia":

                if( $param[ $fieldName ] == "S" ) {
                    TQuickForm::showField( "form_list_bau", "datatransferencia" );
                    TQuickForm::showField( "form_list_bau", "localtransferencia_id" );
                } else {
                    $object->datatransferencia = "";
                    $object->localtransferencia_id = "..::SELECIONE::..";
                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "datatransferencia" );
                    TQuickForm::hideField( "form_list_bau", "localtransferencia_id" );
                }

                break;

            case "alta":

                if( $param[ $fieldName ] == "S" ) {

                    TQuickForm::showField( "form_list_bau", "medicoalta_id" );
                    TQuickForm::showField( "form_list_bau", "horaaltahospitalar" );
                    TQuickForm::showField( "form_list_bau", "dataaltahospitalar" );
                    TQuickForm::showField( "form_list_bau", "tipoaltahospitalar_id" );

                } else {

                    $object->medicoalta_id = "";
                    $object->horaaltahospitalar = "";
                    $object->dataaltahospitalar = "";
                    $object->tipoaltahospitalar_id = "";

                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "medicoalta_id" );
                    TQuickForm::hideField( "form_list_bau", "horaaltahospitalar" );
                    TQuickForm::hideField( "form_list_bau", "dataaltahospitalar" );
                    TQuickForm::hideField( "form_list_bau", "tipoaltahospitalar_id" );

                }

                break;

            case "obito":

                if( $param[ $fieldName ] == "S" ) {

                    TQuickForm::showField( "form_list_bau", "dataobito" );
                    TQuickForm::showField( "form_list_bau", "horaobito" );
                    TQuickForm::showField( "form_list_bau", "declaracaoobitodata" );
                    TQuickForm::showField( "form_list_bau", "declaracaoobitohora" );
                    TQuickForm::showField( "form_list_bau", "destinoobito_id" );
                    TQuickForm::showField( "form_list_bau", "declaracaoobitomedico_id" );

                } else {

                    $object->dataobito = "";
                    $object->horaobito = "";
                    $object->declaracaoobitodata = "";
                    $object->declaracaoobitohora = "";
                    $object->destinoobito_id = "";
                    $object->declaracaoobitomedico_id = "";

                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "dataobito" );
                    TQuickForm::hideField( "form_list_bau", "horaobito" );
                    TQuickForm::hideField( "form_list_bau", "declaracaoobitodata" );
                    TQuickForm::hideField( "form_list_bau", "declaracaoobitohora" );
                    TQuickForm::hideField( "form_list_bau", "destinoobito_id" );
                    TQuickForm::hideField( "form_list_bau", "declaracaoobitomedico_id" );

                }

                break;

        }
    }
}
