<?php

class FarmaciaForm extends TWindow
{

    private $form;

    public function __construct()
    {

        parent::__construct();
        parent::setTitle( "Cadastro de Farmácias " );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder('form_farmacia');
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden('id');
        $nomefarmacia = new TEntry('nomefarmacia');
        $situacao = new TCombo('situacao');
        $tipofarmacia = new TCombo('tipofarmacia');
        $unidadesaude_id = new TDBCombo("unidadesaude_id", "database", "UnidadeDeSaudeRecord", "id", "nomeunidade", "nomeunidade" );


        $situacao->setDefaultOption('::..SELECIONE..::');
        $situacao->addItems([ 'ATIVO'=>'ATIVO','INATIVO'=>'INATIVO']);

        $tipofarmacia->setDefaultOption('::..SELECIONE..::');
        $tipofarmacia->addItems([ 'CENTRAL'=>'CENTRAL','SETORIA'=>'SETORIA']);


        $nomefarmacia->setProperty("title", "O campo e obrigatorio");
        $unidadesaude_id ->setProperty("title", "O campo e obrigatorio");

        $nomefarmacia->setSize("38%");
        $unidadesaude_id->setSize("38%");
        $situacao->setSize("38%");
        $tipofarmacia->setSize("38%");
        
        $nomefarmacia->addValidation( TextFormat::set( "Nome da Farmácia " ), new TRequiredValidator );
        $unidadesaude_id->addValidation( TextFormat::set( "Unidade de Saúde" ), new TRequiredValidator );
        $situacao->addValidation( TextFormat::set( "Situação" ), new TRequiredValidator );
        $tipofarmacia->addValidation( TextFormat::set( "Tipo Farmacia" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome da Farmácia: $redstar")], [$nomefarmacia]);
        $this->form->addFields([new TLabel("Nome da Unidade de Saúde: $redstar")], [$unidadesaude_id]);
        $this->form->addFields([new TLabel("Tipo da Farmacia: $redstar")], [$tipofarmacia]);
        $this->form->addFields([new TLabel("Situação da Farmácia: $redstar")], [$situacao]);


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

            TTransaction::open('database');
                $object = $this->form->getData('FarmaciaRecord');
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "FarmaciaList", "onReload" ] );
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

                $object = new FarmaciaRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }

    }


}
