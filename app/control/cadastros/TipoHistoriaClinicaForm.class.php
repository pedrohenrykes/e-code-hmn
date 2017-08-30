<?php

class TipoHistoriaClinicaForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Tipo de História Clínica" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_tipo_historia_clinica" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                   = new THidden( "id" );
        $nometipohistoriaclinica = new TEntry("nometipohistoriaclinica");
        $situacao       = new TCombo( "situacao" );

        $nometipohistoriaclinica->setProperty("title", "O campo e obrigatorio");

        $nometipohistoriaclinica->setSize("38%");
        $situacao->setSize("38%");

        $situacao->addItems( [ "ATIVO" => "ATIVO", "INATIVO" => "INATIVO" ] );
        $situacao->setDefaultOption( "..::SELECIONE::.." );

        $nometipohistoriaclinica->addValidation( TextFormat::set( "Tipo de História Clínica" ), new TRequiredValidator );
        $situacao->addValidation( TextFormat::set( "Situação" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Tipo de História Clínica: $redstar")], [$nometipohistoriaclinica]);
        $this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "TipoHistoriaClinicaList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );
                $object = $this->form->getData("TipoHistoriaClinicaRecord");
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "TipoHistoriaClinicaList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );
                    $object = new TipoHistoriaClinicaRecord($param["key"]);
                    $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
