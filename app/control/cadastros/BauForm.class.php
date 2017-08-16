<?php

class BauForm extends TPage
{
    private $form;
    public function __construct()
    {
        parent::__construct();
       
        $this->form = new BootstrapFormBuilder( "form_bau" );
        $this->form->setFormTitle( "Cadastro de BAU" );
        $this->form->class = "tform";

        $paciente_id = new TCombo('paciente_id');
        $id = new THidden('id');
        $dataentrada = new TDate('dataentrada');
        $horaentrada = new TEntry('horaentrada');
        $internamentolocal = new TEntry('internamentolocal');
        $datainternamento = new TDate('datainternamento');
        $remocao = new TEntry('remocao');
        $dataremocao = new TDate('dataremocao');
        $localremocao_id = new TCombo('localremocao_id');
        $transferencia = new TEntry('transferencia');
        $datatransferencia = new TDate('datatransferencia');
        $localtransferencia_id = new TCombo('localtransferencia_id');
        $transportedestino_id = new TCombo('transportedestino_id');
        $especificartransporte = new TEntry('especificartransporte');
        $datatransporte = new TDate('datatransporte');
        $tipoaltahospitalar_id = new TCombo('tipoaltahospitalar_id');
        $dataaltahospitalar = new TDate('dataaltahospitalar');
        $horaaltahospitalar = new TEntry('horaaltahospitalar');
        $medicoalta_id = new TCombo('medicoalta_id');
        $dataobito = new TDate('dataobito');
        $horaobito = new TEntry('horaobito');
        $destinoobito_id = new TCombo('destinoobito_id');
        $declaracaoobitodata = new TDate('declaracaoobitodata');
        $declaracaoobitohora = new TEntry('declaracaoobitohora');
        $declaracaoobitomedico_id = new TCombo('declaracaoobitomedico_id');
        $convenio_id = new TCombo('convenio_id');
        $queixaprincipal = new TEntry('queixaprincipal');

        $internamentolocal->setMaxLength(3);
        $remocao->setMaxLength(3);
        $transferencia->setMaxLength(3);
        $especificartransporte->setMaxLength(40);
        $queixaprincipal->setMaxLength(255);

        $dataentrada->setMask( "dd/mm/yyyy" );
        $datainternamento->setMask( "dd/mm/yyyy" );
        $dataremocao->setMask( "dd/mm/yyyy" );
        $datatransferencia->setMask( "dd/mm/yyyy" );
        $datatransporte->setMask( "dd/mm/yyyy" );
        $dataaltahospitalar->setMask( "dd/mm/yyyy" );
        $dataobito->setMask( "dd/mm/yyyy" );
        $declaracaoobitodata->setMask( "dd/mm/yyyy" );

        $dataentrada->setDatabaseMask('yyyy-mm-dd');
        $datainternamento->setDatabaseMask('yyyy-mm-dd');
        $dataremocao->setDatabaseMask('yyyy-mm-dd');
        $datatransferencia->setDatabaseMask('yyyy-mm-dd');
        $datatransporte->setDatabaseMask('yyyy-mm-dd');
        $dataaltahospitalar->setDatabaseMask('yyyy-mm-dd');
        $dataobito->setDatabaseMask('yyyy-mm-dd');
        $declaracaoobitodata->setDatabaseMask('yyyy-mm-dd');

        $convenio_id->addValidation('Convenio' , new TRequiredValidator);
        $paciente_id->addValidation('Paciente' , new TRequiredValidator);

        $this->form->addFields( [ new TLabel( "Paciente:<font color=red><b>*</b></font> ") ], [ $paciente_id ] );
        $this->form->addFields( [ new TLabel( "Dia:<font color=red>*</font>" ) ], [ $dataentrada ] );
        $this->form->addFields( [ new TLabel( "Hora:" ) ], [ $horaentrada ] );
        $this->form->addFields( [ new TLabel( "Internamento local:<font color=red><b>*</b></font>" ) ], [ $internamentolocal ]);
        $this->form->addFields( [ new TLabel( "datainternamento:<font color=red>*</font>" ) ], [ $datainternamento ] );
        $this->form->addFields( [ new TLabel( "remocao:<font color=red>* ") ], [ $remocao ] );
        $this->form->addFields( [ new TLabel( "dataremocao:<font color=red><b>*</b></font>") ], [ $dataremocao ] );
        $this->form->addFields( [ new TLabel( "localremocao_id:<font color=red><b>*</b></font>") ], [ $localremocao_id ] );
        $this->form->addFields( [ new TLabel( "transferencia:<font color=red>*</font>") ], [ $transferencia ] );
        $this->form->addFields( [ new TLabel( "datatransferencia:<font color=red>*</font>" ) ], [ $datatransferencia ] );

        $this->form->addFields( [ new TLabel( "localtransferencia_id:" ) ], [ $localtransferencia_id ] );
        $this->form->addFields( [ new TLabel( "transportedestino_id:" ) ], [ $transportedestino_id ] );
        $this->form->addFields( [ new TLabel( "especificartransporte:" ) ], [ $especificartransporte ] );
        $this->form->addFields( [ new TLabel( "datatransporte:" ) ], [ $datatransporte ] );
        $this->form->addFields( [ new TLabel( "tipoaltahospitalar_id:" ) ], [ $tipoaltahospitalar_id ] );
        $this->form->addFields( [ new TLabel( "dataaltahospitalar:" ) ], [ $dataaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "horaaltahospitalar:" ) ], [ $horaaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "medicoalta_id:" ) ], [ $medicoalta_id ] );
        $this->form->addFields( [ new TLabel( "dataobito:" ) ], [ $dataobito ] );
        $this->form->addFields( [ new TLabel( "horaobito:" ) ], [ $horaobito ] );
        $this->form->addFields( [ new TLabel( "destinoobito_id:" ) ], [ $destinoobito_id ] );
        $this->form->addFields( [ new TLabel( "declaracaoobitodata:" ) ], [ $declaracaoobitodata ] );
        $this->form->addFields( [ new TLabel( "declaracaoobitohora:" ) ], [ $declaracaoobitohora ] );
        $this->form->addFields( [ new TLabel( "declaracaoobitomedico_id:" ) ], [ $declaracaoobitomedico_id ] );
        $this->form->addFields( [ new TLabel( "convenio_id:" ) ], [ $convenio_id ] );
        $this->form->addFields( [ new TLabel( "queixaprincipal:" ) ], [ $queixaprincipal ] );
        
        $this->form->addFields( [new TLabel('<font color=red><b>* Campos Obrigatórios </b></font>'), []] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "PacienteList", "onReload" ] ), "fa:table blue" );
      
        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );
        parent::add( $container );

    }
    public function onSave()
    {
        try
        {

            $this->form->validate();
            TTransaction::open( "database" );

            $object = $this->form->getData( "BauRecord" );
            $object->store();
            
            TTransaction::close();
            $action = new TAction( [ "BauList", "onReload" ] );
            new TMessage( "info", "Registro salvo com sucesso!", $action );
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br>" . $ex->getMessage() );
        }
    }
    public function onEdit( $param )
    {
        try
        {
            if( isset( $param[ "key" ] ) )
            {
                TTransaction::open( "dbsic" );
                $object = new BauRecord( $param[ "key" ] );
                //$object->nascimento = TDate::date2br( $object->nascimento );
                $this->form->setData( $object );
                TTransaction::close();
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}