<?php

class BauForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_bau" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                       = new THidden('id');
        $especificartransporte    = new TEntry('especificartransporte');
        $queixaprincipal          = new TText('queixaprincipal');
        $dataentrada              = new TDate('dataentrada');
        $horaentrada              = new TDate('horaentrada');
        $dataaltahospitalar       = new TDate('dataaltahospitalar');
        $horaaltahospitalar       = new TDate('horaaltahospitalar');
        $dataobito                = new TDate('dataobito');
        $horaobito                = new TDate('horaobito');
        $declaracaoobitodata      = new TDate('declaracaoobitodata');
        $declaracaoobitohora      = new TDate('declaracaoobitohora');
        $datainternamento         = new TDate('datainternamento');
        $dataremocao              = new TDate('dataremocao');
        $datatransferencia        = new TDate('datatransferencia');
        $datatransporte           = new TDate('datatransporte');
        $remocao                  = new TCombo('remocao');
        $transferencia            = new TCombo('transferencia');
        $internamentolocal        = new TCombo('internamentolocal');

        $paciente_id                = new THidden( "paciente_id" );
        $paciente_nome              = new TDBSeekButton( "paciente_nome", "database", "form_bau", "PacienteRecord", "nomepaciente", "paciente_id", "paciente_nome" );

        $localremocao_id          = new TCombo('localremocao_id');
        $localtransferencia_id    = new TCombo('localtransferencia_id');
        $transportedestino_id     = new TCombo('transportedestino_id');
        $tipoaltahospitalar_id    = new TCombo('tipoaltahospitalar_id');
        $medicoalta_id            = new TCombo('medicoalta_id');
        $destinoobito_id          = new TCombo('destinoobito_id');
        $declaracaoobitomedico_id = new TCombo('declaracaoobitomedico_id');
        $responsavel_id           = new TCombo('responsavel_id');
        $convenio_id              = new TDBCombo( "convenio_id", "database", "ConvenioRecord", "id", "nome", "nome");

        $id                      ->setSize( "38%" );
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
        $paciente_nome           ->setSize( "275" );
        $localremocao_id         ->setSize( "38%" );
        $localtransferencia_id   ->setSize( "38%" );
        $transportedestino_id    ->setSize( "38%" );
        $tipoaltahospitalar_id   ->setSize( "38%" );
        $medicoalta_id           ->setSize( "38%" );
        $destinoobito_id         ->setSize( "38%" );
        $declaracaoobitomedico_id->setSize( "38%" );
        $responsavel_id          ->setSize( "38%" );
        $convenio_id             ->setSize( "38%" );

        $remocao                 ->setDefaultOption( "..::SELECIONE::.." );
        $transferencia           ->setDefaultOption( "..::SELECIONE::.." );
        $internamentolocal       ->setDefaultOption( "..::SELECIONE::.." );
        $localremocao_id         ->setDefaultOption( "..::SELECIONE::.." );
        $localtransferencia_id   ->setDefaultOption( "..::SELECIONE::.." );
        $transportedestino_id    ->setDefaultOption( "..::SELECIONE::.." );
        $tipoaltahospitalar_id   ->setDefaultOption( "..::SELECIONE::.." );
        $medicoalta_id           ->setDefaultOption( "..::SELECIONE::.." );
        $destinoobito_id         ->setDefaultOption( "..::SELECIONE::.." );
        $declaracaoobitomedico_id->setDefaultOption( "..::SELECIONE::.." );
        $responsavel_id          ->setDefaultOption( "..::SELECIONE::.." );
        $convenio_id             ->setDefaultOption( "..::SELECIONE::.." );

        $remocao          ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $transferencia    ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $internamentolocal->addItems( [ "S" => "SIM", "N" => "NÃO" ] );

        $dataentrada        ->setMask( "dd/mm/yyyy" );
        $dataentrada        ->setDatabaseMask('yyyy-mm-dd');
        $datainternamento   ->setMask( "dd/mm/yyyy" );
        $datainternamento   ->setDatabaseMask('yyyy-mm-dd');
        $dataremocao        ->setMask( "dd/mm/yyyy" );
        $dataremocao        ->setDatabaseMask('yyyy-mm-dd');
        $datatransferencia  ->setMask( "dd/mm/yyyy" );
        $datatransferencia  ->setDatabaseMask('yyyy-mm-dd');
        $datatransporte     ->setMask( "dd/mm/yyyy" );
        $datatransporte     ->setDatabaseMask('yyyy-mm-dd');
        $dataaltahospitalar ->setMask( "dd/mm/yyyy" );
        $dataaltahospitalar ->setDatabaseMask('yyyy-mm-dd');
        $dataobito          ->setMask( "dd/mm/yyyy" );
        $dataobito          ->setDatabaseMask('yyyy-mm-dd');
        $declaracaoobitodata->setMask( "dd/mm/yyyy" );
        $declaracaoobitodata->setDatabaseMask('yyyy-mm-dd');

        $label01 = new RequiredTextFormat( [ "Nome do Paciente", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Sexo", "#F00", "bold" ] );

        $convenio_id->addValidation( $label01->getText(), new TRequiredValidator );
        $paciente_id->addValidation( $label01->getText(), new TRequiredValidator );

        $page1 = new TLabel( "Paciente", '#7D78B6', 12, 'bi');
        $page1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->appendPage( "Identificação" );
        $this->form->addContent( [ $page1 ] );
        $this->form->addFields( [ new TLabel( "Nome do Paciente: {$redstar}") ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data de Entrada: {$redstar}" ) ], [ $dataentrada ] );
        $this->form->addFields( [ new TLabel( "Hora de Entrada:" ) ], [ $horaentrada ] );
        $this->form->addFields( [ new TLabel( "Queixa Principal:" ) ], [ $queixaprincipal ] );
        $this->form->addFields( [ new TLabel( "Responsável:") ], [ $responsavel_id ] );
        $this->form->addFields( [ new TLabel( "Convênio:" ) ], [ $convenio_id ] );
        $this->form->addFields( [ $id, $paciente_id ] );

        $page2 = new TLabel( "Estado Geral", '#7D78B6', 12, 'bi' );
        $page2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->appendPage( "Avaliação" );
        $this->form->addContent( [ $page2 ] );
        // TODO rever a questão de relacionamento com a tabela de classificação de risco

        $page3 = new TLabel( "Internamento", '#7D78B6', 12, 'bi' );
        $page3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->appendPage( "Destinação" );
        $this->form->addContent( [ $page3 ] );
        $this->form->addFields( [ new TLabel( "Internamento Local: {$redstar}" ) ], [ $internamentolocal ]);
        $this->form->addFields( [ new TLabel( "Data de Internamento: {$redstar}" ) ], [ $datainternamento ] );

        $page4 = new TLabel( "Remoção", '#7D78B6', 12, 'bi' );
        $page4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $page4 ] );
        $this->form->addFields( [ new TLabel( "Remoção: {$redstar}") ], [ $remocao ] );
        $this->form->addFields( [ new TLabel( "Data de Remoção: {$redstar}") ], [ $dataremocao ] );
        $this->form->addFields( [ new TLabel( "Local de Remoção: {$redstar}") ], [ $localremocao_id ] );

        $page5 = new TLabel( "Transferência", '#7D78B6', 12, 'bi');
        $page5->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $page5 ] );
        $this->form->addFields( [ new TLabel( "Transferência: {$redstar}") ], [ $transferencia ] );
        $this->form->addFields( [ new TLabel( "Data de Transferência: {$redstar}" ) ], [ $datatransferencia ] );
        $this->form->addFields( [ new TLabel( "Local de Transferência:" ) ], [ $localtransferencia_id ] );

        $page6 = new TLabel( "Transporte", '#7D78B6', 12, 'bi');
        $page6->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $page6 ] );
        $this->form->addFields( [ new TLabel( "Destino do Transporte:" ) ], [ $transportedestino_id ] );
        $this->form->addFields( [ new TLabel( "Informações do Transporte:" ) ], [ $especificartransporte ] );
        $this->form->addFields( [ new TLabel( "Data do Transporte:" ) ], [ $datatransporte ] );

        $page7 = new TLabel( "Alta Hospitalar", '#7D78B6', 12, 'bi');
        $page7->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $page7 ] );
        $this->form->addFields( [ new TLabel( "Tipo de Alta:" ) ], [ $tipoaltahospitalar_id ] );
        $this->form->addFields( [ new TLabel( "Data da Alta:" ) ], [ $dataaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "Hora da Alta:" ) ], [ $horaaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "Médico Responsável:" ) ], [ $medicoalta_id ] );

        $page7 = new TLabel( "Declaração de Óbito", '#7D78B6', 12, 'bi');
        $page7->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $page7 ] );
        $this->form->addFields( [ new TLabel( "Data do Óbito:" ) ], [ $dataobito ] );
        $this->form->addFields( [ new TLabel( "Hora do Óbito:" ) ], [ $horaobito ] );
        $this->form->addFields( [ new TLabel( "Data da Declaração:" ) ], [ $declaracaoobitodata ] );
        $this->form->addFields( [ new TLabel( "Hora da Declaração:" ) ], [ $declaracaoobitohora ] );
        $this->form->addFields( [ new TLabel( "Destino do Corpo:" ) ], [ $destinoobito_id ] );
        $this->form->addFields( [ new TLabel( "Medico Responsável:" ) ], [ $declaracaoobitomedico_id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "BauList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "BauRecord" );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "BauList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "dbsic" );

                $object = new BauRecord( $param[ "key" ] );
                //$object->nascimento = TDate::date2br( $object->nascimento );

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
