<?php
/**
 *
 * @author Danilo Cunha
 */

class DocenteForm extends TWindow
{
    protected $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Formulário de Docentes" );
        parent::setSize(0.600, 0.800);

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_docente" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden("id");
        $chapa = new TEntry("chapa");
        $nomedocente = new TEntry("nomedocente");
        $email = new TEntry("email");
        $contato = new TEntry("contato");

        $email->setProperty( "placeholder", "Ex.: example@email.com" );
        $contato->setProperty( "placeholder", "Ex.: (84)99999-8888" );

        $chapa->setMask('9!');
        $contato->setMask("(99)99999-9999");

        $email->forceLowerCase();

        $chapa->setSize( "38%" );
        $nomedocente->setSize( "38%" );
        $email->setSize( "38%" );
        $contato->setSize( "38%" );

        $label01 = new RequiredTextFormat( [ "Chapa", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Nome", "#F00", "bold" ] );
        $label03 = new RequiredTextFormat( [ "E-mail", "#F00", "bold" ] );

        $chapa->addValidation( $label01->getText(), new TRequiredValidator );
        $nomedocente->addValidation( $label02->getText(), new TRequiredValidator );
        $email->addValidation( $label03->getText(), new TEmailValidator);

        $this->form->addFields( [ new TLabel( "Chapa: $redstar" ) ], [ $chapa ] );
        $this->form->addFields( [ new TLabel( "Nome: $redstar" ) ], [ $nomedocente ] );
        $this->form->addFields( [ new TLabel( "E-mail:" ) ], [ $email ] );
        $this->form->addFields( [ new TLabel( "Telefone:" ) ], [ $contato ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction("Salvar", new TAction([$this, "onSave"]), "fa:floppy-o");

        $container = new TVBox();
        $container->style = "width: 100%";
        // $container->add(new TXMLBreadCrumb("menu.xml", "DocenteList"));
        $container->add($this->form);
        parent::add($container);
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "DocenteRecord" );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ 'DocenteList', 'onReload' ] );

            new TMessage( 'info', 'Registro salvo com sucesso!', $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>' . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new ClinicaRecord( $param[ "key" ] );

                TTransaction::close();

                $this->form->setData( $object );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
