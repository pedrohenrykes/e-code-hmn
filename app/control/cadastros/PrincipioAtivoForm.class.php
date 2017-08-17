<?php

class PrincipioAtivoForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Princípio Ativo" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_principioativo" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                  = new THidden( "id" );
        $nomeprincipioativo    = new TEntry( "nomeprincipioativo" );
       
        //$nomeprofissional->forceUpperCase();
        //$numeroconselho->setMask( "A!" );

        $nomeprincipioativo->setProperty("title", "O campo e obrigatorio");
      
        $nomeprincipioativo->setSize("38%");

        $label01 = new RequiredTextFormat( [ "Nome do Princípio Ativo", "#F00", "bold" ] );

        $nomeprincipioativo->addValidation( $label01->getText(), new TRequiredValidator );
        
        $this->form->addFields([new TLabel("Nome do Princípio Ativo: $redstar")], [$nomeprincipioativo]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "PrincipioAtivoList", "onReload" ] ), "fa:table blue" );

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

                $object = $this->form->getData("PrincipioAtivoRecord");
                $object->store();

            TTransaction::close();

            $action = new TAction( [ "PrincipioAtivoList", "onReload" ] );

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

                $object = new PrincipioAtivoRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
