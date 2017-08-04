<?php
/*
 * @author Pedro Henrique
 * @date 27/04/2016
 */

class DashboardCreate extends TPage
{
    private $dashboard;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->dashboard = new TElement("div");
        $this->dashboard->class = "row";

        $container = new TVBox();
        $container->style = "width: 100%";
        TBreadCrumb::setHomeController("DashboardCreate");
        $container->add(TBreadCrumb::create(["Painel de controle"], TRUE) . '<br>');
        $container->add($this->dashboard);

        parent::add($container);
    }

    private function createItem($param = null)
    {
        $divrow = new TElement("div");
        $divcolumn = new TElement("div");
        $divsbox = new TElement("div");
        $divinfo = new TElement("div");
        $divicon = new TElement("div");
        $action = new TElement("a");
        $number = new TElement("h3");
        $title = new TElement("p");
        $dashicon = new TElement("i");
        $actionicon = new TElement("i");

        $divcolumn->class = "col-lg-3 col-xs-6";
        $divsbox->class = "small-box " . $param->color;
        $divinfo->class = "inner";
        $divicon->class = "icon";
        $action->class = "small-box-footer";
        $action->href = "index.php?class=" . $param->page . "&method=" . $param->action;
        $action->add("Mais ");
        $number->add($param->quantifier);
        $title->add($param->title);
        $dashicon->class = "fa " . $param->icon;
        $actionicon->class = "fa fa-arrow-circle-right";

        $this->dashboard->add($divcolumn);
        $divcolumn->add($divsbox);
        $divsbox->add($divinfo);
        $divsbox->add($divicon);
        $divsbox->add($action);
        $divinfo->add($number);
        $divinfo->add($title);
        $divicon->add($dashicon);
        $action->add($actionicon);
    }

    public function loadData()
    {
        if (!$this->loaded) {

            try {

                TTransaction::open("database");

                $repository = new TRepository("DashboardModel");

                $criteria = new TCriteria();
                $criteria->setProperties([
                    "order" => "id",
                    "direction" => "asc",
                ]);

                $objects = $repository->load($criteria, FALSE);

                if ($objects) {

                    $conn = TTransaction::get();

                    foreach ($objects as $object) {

                        if (isset($object->dataview)) {

                            $result = $conn->query(
                                "SELECT " . $object->quantifier .
                                " FROM " . $object->dataview
                            );

                            foreach ($result as $row) {
                                $object->quantifier = $row[$object->quantifier];
                            }

                        }

                        $this->createItem($object);
                    }
                }

                TTransaction::close();

                $this->loaded = TRUE;

            } catch (Exception $ex) {

                TTransaction::rollback();

                new TMessage("error", $ex->getMessage());

            }
        }
    }

    public function show()
    {
        $this->loadData();

        parent::show();
    }
}
