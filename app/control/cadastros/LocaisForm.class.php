<?php

class LocaisForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Profissionais" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_locais" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id        = new THidden( "id" );
        $nomelocal = new TEntry( "nomelocal" );
        $situacao = new TCombo( "situacao" );

        //$nomelocal->forceUpperCase();
        //$numeroconselho->setMask( "A!" );

        $nomelocal->setProperty("title", "O campo e obrigatorio");
        $nomelocal->setSize("38%");
        $nomelocal->addValidation( TextFormat::set( "Nome dos Locais" ), new TRequiredValidator );

        $situacao->setProperty("title", "O campo e obrigatorio");
        $situacao->setSize("38%");
        $situacao->addValidation( TextFormat::set( "Situação" ), new TRequiredValidator );

        $situacao->addItems( [ "ATIVO" => "ATIVO", "INATIVO" => "INATIVO" ] );
        $situacao->setDefaultOption( "..::SELECIONE::.." );

        $this->form->addFields([new TLabel("Nome dos Locais: $redstar")], [$nomelocal]);
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

            $object = $this->form->getData("LocaisRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "LocaisList", "onReload" ] );

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

                $object = new LocaisRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
