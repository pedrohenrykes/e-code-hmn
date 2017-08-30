<?php

class MedicamentoForm extends TWindow
{

    private $form;

    public function __construct()
    {

        parent::__construct();
        parent::setTitle( "Cadastro de Medicamentos" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder('form_medicamento');
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden('id');
        $nomemedicamento = new TEntry('nomemedicamento');
        $principioativo_id = new TDBCombo("principioativo_id", "database", "PrincipioAtivoRecord", "id", "nomeprincipioativo", "nomeprincipioativo" );

        $nomemedicamento->setProperty("title", "O campo e obrigatorio");
        $principioativo_id ->setProperty("title", "O campo e obrigatorio");

        $nomemedicamento->setSize("38%");
        $principioativo_id->setSize("38%");
        
        $nomemedicamento->addValidation( TextFormat::set( "Nome do Medicamento" ), new TRequiredValidator );
        $principioativo_id->addValidation( TextFormat::set( "Principio ativo" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome do Medicamento: $redstar")], [$nomemedicamento]);
        $this->form->addFields([new TLabel("Nome do Princípio Ativo: $redstar")], [$principioativo_id]);


        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "MedicamentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );

    }

    public function onSave()
    {

        try {

            $this->form->validate();

            TTransaction::open('database');
                $object = $this->form->getData('MedicamentoRecord');
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "MedicamentoList", "onReload" ] );
            new TMessage("info", "Registro salvo com sucesso!", $action);

        } catch (Exception $ex) {

            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    function onEdit($param)
    {

        try {

            if (isset($param['key'])) {

                TTransaction::open( "database" );

                $object = new MedicamentoRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }

    }


}
