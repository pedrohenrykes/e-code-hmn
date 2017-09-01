<?php

class TransporteForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro Transporte" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_transporte" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                   = new THidden( "id" );
        $nometransporte = new TEntry("nometransporte");
        $situacao       = new TCombo( "situacao" );

        $nometransporte->setProperty("title", "O campo e obrigatorio");

        $nometransporte->setSize("38%");
        $situacao->setSize("38%");

        $situacao->addItems( [ "ATIVO" => "ATIVO", "INATIVO" => "INATIVO" ] );
        $situacao->setDefaultOption( "..::SELECIONE::.." );

        $nometransporte->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );
        $situacao->addValidation( TextFormat::set( "Situação" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome Transporte: $redstar")], [$nometransporte]);
        $this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

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
                $object = $this->form->getData("TransporteRecord");
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "TransporteList", "onReload" ] );

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
                    $object = new TransporteRecord($param["key"]);
                    $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
