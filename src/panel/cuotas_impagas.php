<?php
//Include Common Files @1-B3513254
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "cuotas_impagas.php");
include_once(RelativePath . "/Common.php");
include_once(RelativePath . "/Template.php");
include_once(RelativePath . "/Sorter.php");
include_once(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Report1 ReportGroup class @3-4D9A1196
class clsReportGroupReport1 {
    var $GroupType;
    var $mode; //1 - open, 2 - close
    var $id, $_idAttributes;
    var $nombre, $_nombreAttributes;
    var $anio, $_anioAttributes;
    var $mes, $_mesAttributes;
    var $Attributes;
    var $ReportTotalIndex = 0;
    var $PageTotalIndex;
    var $PageNumber;
    var $RowNumber;
    var $Parent;

    function clsReportGroupReport1(& $parent) {
        $this->Parent = & $parent;
        $this->Attributes = $this->Parent->Attributes->GetAsArray();
    }
    function SetControls($PrevGroup = "") {
        $this->id = $this->Parent->id->Value;
        $this->nombre = $this->Parent->nombre->Value;
        $this->anio = $this->Parent->anio->Value;
        $this->mes = $this->Parent->mes->Value;
    }

    function SetTotalControls($mode = "", $PrevGroup = "") {
        $this->_Sorter_idAttributes = $this->Parent->Sorter_id->Attributes->GetAsArray();
        $this->_Sorter_nombreAttributes = $this->Parent->Sorter_nombre->Attributes->GetAsArray();
        $this->_Sorter_anioAttributes = $this->Parent->Sorter_anio->Attributes->GetAsArray();
        $this->_Sorter_mesAttributes = $this->Parent->Sorter_mes->Attributes->GetAsArray();
        $this->_idAttributes = $this->Parent->id->Attributes->GetAsArray();
        $this->_nombreAttributes = $this->Parent->nombre->Attributes->GetAsArray();
        $this->_anioAttributes = $this->Parent->anio->Attributes->GetAsArray();
        $this->_mesAttributes = $this->Parent->mes->Attributes->GetAsArray();
        $this->_NavigatorAttributes = $this->Parent->Navigator->Attributes->GetAsArray();
    }
    function SyncWithHeader(& $Header) {
        $this->id = $Header->id;
        $Header->_idAttributes = $this->_idAttributes;
        $this->Parent->id->Value = $Header->id;
        $this->Parent->id->Attributes->RestoreFromArray($Header->_idAttributes);
        $this->nombre = $Header->nombre;
        $Header->_nombreAttributes = $this->_nombreAttributes;
        $this->Parent->nombre->Value = $Header->nombre;
        $this->Parent->nombre->Attributes->RestoreFromArray($Header->_nombreAttributes);
        $this->anio = $Header->anio;
        $Header->_anioAttributes = $this->_anioAttributes;
        $this->Parent->anio->Value = $Header->anio;
        $this->Parent->anio->Attributes->RestoreFromArray($Header->_anioAttributes);
        $this->mes = $Header->mes;
        $Header->_mesAttributes = $this->_mesAttributes;
        $this->Parent->mes->Value = $Header->mes;
        $this->Parent->mes->Attributes->RestoreFromArray($Header->_mesAttributes);
    }
    function ChangeTotalControls() {
    }
}
//End Report1 ReportGroup class

//Report1 GroupsCollection class @3-E374462A
class clsGroupsCollectionReport1 {
    var $Groups;
    var $mPageCurrentHeaderIndex;
    var $PageSize;
    var $TotalPages = 0;
    var $TotalRows = 0;
    var $CurrentPageSize = 0;
    var $Pages;
    var $Parent;
    var $LastDetailIndex;

    function clsGroupsCollectionReport1(& $parent) {
        $this->Parent = & $parent;
        $this->Groups = array();
        $this->Pages  = array();
        $this->mReportTotalIndex = 0;
        $this->mPageTotalIndex = 1;
    }

    function & InitGroup() {
        $group = new clsReportGroupReport1($this->Parent);
        $group->RowNumber = $this->TotalRows + 1;
        $group->PageNumber = $this->TotalPages;
        $group->PageTotalIndex = $this->mPageCurrentHeaderIndex;
        return $group;
    }

    function RestoreValues() {
        $this->Parent->id->Value = $this->Parent->id->initialValue;
        $this->Parent->nombre->Value = $this->Parent->nombre->initialValue;
        $this->Parent->anio->Value = $this->Parent->anio->initialValue;
        $this->Parent->mes->Value = $this->Parent->mes->initialValue;
    }

    function OpenPage() {
        $this->TotalPages++;
        $Group = & $this->InitGroup();
        $this->Parent->Page_Header->CCSEventResult = CCGetEvent($this->Parent->Page_Header->CCSEvents, "OnInitialize", $this->Parent->Page_Header);
        if ($this->Parent->Page_Header->Visible)
            $this->CurrentPageSize = $this->CurrentPageSize + $this->Parent->Page_Header->Height;
        $Group->SetTotalControls("GetNextValue");
        $this->Parent->Page_Header->CCSEventResult = CCGetEvent($this->Parent->Page_Header->CCSEvents, "OnCalculate", $this->Parent->Page_Header);
        $Group->SetControls();
        $Group->Mode = 1;
        $Group->GroupType = "Page";
        $Group->PageTotalIndex = count($this->Groups);
        $this->mPageCurrentHeaderIndex = count($this->Groups);
        $this->Groups[] =  & $Group;
        $this->Pages[] =  count($this->Groups) == 2 ? 0 : count($this->Groups) - 1;
    }

    function OpenGroup($groupName) {
        $Group = "";
        $OpenFlag = false;
        if ($groupName == "Report") {
            $Group = & $this->InitGroup(true);
            $this->Parent->Report_Header->CCSEventResult = CCGetEvent($this->Parent->Report_Header->CCSEvents, "OnInitialize", $this->Parent->Report_Header);
            if ($this->Parent->Report_Header->Visible) 
                $this->CurrentPageSize = $this->CurrentPageSize + $this->Parent->Report_Header->Height;
                $Group->SetTotalControls("GetNextValue");
            $this->Parent->Report_Header->CCSEventResult = CCGetEvent($this->Parent->Report_Header->CCSEvents, "OnCalculate", $this->Parent->Report_Header);
            $Group->SetControls();
            $Group->Mode = 1;
            $Group->GroupType = "Report";
            $this->Groups[] = & $Group;
            $this->OpenPage();
        }
    }

    function ClosePage() {
        $Group = & $this->InitGroup();
        $this->Parent->Page_Footer->CCSEventResult = CCGetEvent($this->Parent->Page_Footer->CCSEvents, "OnInitialize", $this->Parent->Page_Footer);
        $Group->SetTotalControls("GetPrevValue");
        $Group->SyncWithHeader($this->Groups[$this->mPageCurrentHeaderIndex]);
        $this->Parent->Page_Footer->CCSEventResult = CCGetEvent($this->Parent->Page_Footer->CCSEvents, "OnCalculate", $this->Parent->Page_Footer);
        $Group->SetControls();
        $this->RestoreValues();
        $this->CurrentPageSize = 0;
        $Group->Mode = 2;
        $Group->GroupType = "Page";
        $this->Groups[] = & $Group;
    }

    function CloseGroup($groupName)
    {
        $Group = "";
        if ($groupName == "Report") {
            $Group = & $this->InitGroup(true);
            $this->Parent->Report_Footer->CCSEventResult = CCGetEvent($this->Parent->Report_Footer->CCSEvents, "OnInitialize", $this->Parent->Report_Footer);
            if ($this->Parent->Page_Footer->Visible) 
                $OverSize = $this->Parent->Report_Footer->Height + $this->Parent->Page_Footer->Height;
            else
                $OverSize = $this->Parent->Report_Footer->Height;
            if (($this->PageSize > 0) and $this->Parent->Report_Footer->Visible and ($this->CurrentPageSize + $OverSize > $this->PageSize)) {
                $this->ClosePage();
                $this->OpenPage();
            }
            $Group->SetTotalControls("GetPrevValue");
            $Group->SyncWithHeader($this->Groups[0]);
            if ($this->Parent->Report_Footer->Visible)
                $this->CurrentPageSize = $this->CurrentPageSize + $this->Parent->Report_Footer->Height;
            $this->Parent->Report_Footer->CCSEventResult = CCGetEvent($this->Parent->Report_Footer->CCSEvents, "OnCalculate", $this->Parent->Report_Footer);
            $Group->SetControls();
            $this->RestoreValues();
            $Group->Mode = 2;
            $Group->GroupType = "Report";
            $this->Groups[] = & $Group;
            $this->ClosePage();
            return;
        }
    }

    function AddItem()
    {
        $Group = & $this->InitGroup(true);
        $this->Parent->Detail->CCSEventResult = CCGetEvent($this->Parent->Detail->CCSEvents, "OnInitialize", $this->Parent->Detail);
        if ($this->Parent->Page_Footer->Visible) 
            $OverSize = $this->Parent->Detail->Height + $this->Parent->Page_Footer->Height;
        else
            $OverSize = $this->Parent->Detail->Height;
        if (($this->PageSize > 0) and $this->Parent->Detail->Visible and ($this->CurrentPageSize + $OverSize > $this->PageSize)) {
            $this->ClosePage();
            $this->OpenPage();
        }
        $this->TotalRows++;
        if ($this->LastDetailIndex)
            $PrevGroup = & $this->Groups[$this->LastDetailIndex];
        else
            $PrevGroup = "";
        $Group->SetTotalControls("", $PrevGroup);
        if ($this->Parent->Detail->Visible)
            $this->CurrentPageSize = $this->CurrentPageSize + $this->Parent->Detail->Height;
        $this->Parent->Detail->CCSEventResult = CCGetEvent($this->Parent->Detail->CCSEvents, "OnCalculate", $this->Parent->Detail);
        $Group->SetControls($PrevGroup);
        $this->LastDetailIndex = count($this->Groups);
        $this->Groups[] = & $Group;
    }
}
//End Report1 GroupsCollection class

class clsReportReport1 { //Report1 Class @3-BC2FB08C

//Report1 Variables @3-0467AA43

    var $ComponentType = "Report";
    var $PageSize;
    var $ComponentName;
    var $Visible;
    var $Errors;
    var $CCSEvents = array();
    var $CCSEventResult;
    var $RelativePath = "";
    var $ViewMode = "Web";
    var $TemplateBlock;
    var $PageNumber;
    var $RowNumber;
    var $TotalRows;
    var $TotalPages;
    var $ControlsVisible = array();
    var $IsEmpty;
    var $Attributes;
    var $DetailBlock, $Detail;
    var $Report_FooterBlock, $Report_Footer;
    var $Report_HeaderBlock, $Report_Header;
    var $Page_FooterBlock, $Page_Footer;
    var $Page_HeaderBlock, $Page_Header;
    var $SorterName, $SorterDirection;

    var $ds;
    var $DataSource;
    var $UseClientPaging = false;

    //Report Controls
    var $StaticControls, $RowControls, $Report_FooterControls, $Report_HeaderControls;
    var $Page_FooterControls, $Page_HeaderControls;
    var $Sorter_id;
    var $Sorter_nombre;
    var $Sorter_anio;
    var $Sorter_mes;
//End Report1 Variables

//Class_Initialize Event @3-2AE5C760
    function clsReportReport1($RelativePath = "", & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "Report1";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Attributes = new clsAttributes($this->ComponentName . ":");
        $this->Detail = new clsSection($this);
        $MinPageSize = 0;
        $MaxSectionSize = 0;
        $this->Detail->Height = 1;
        $MaxSectionSize = max($MaxSectionSize, $this->Detail->Height);
        $this->Report_Footer = new clsSection($this);
        $this->Report_Header = new clsSection($this);
        $this->Page_Footer = new clsSection($this);
        $this->Page_Footer->Height = 1;
        $MinPageSize += $this->Page_Footer->Height;
        $this->Page_Header = new clsSection($this);
        $this->Page_Header->Height = 1;
        $MinPageSize += $this->Page_Header->Height;
        $this->Errors = new clsErrors();
        $this->DataSource = new clsReport1DataSource($this);
        $this->ds = & $this->DataSource;
        $PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(is_numeric($PageSize) && $PageSize > 0) {
            $this->PageSize = $PageSize;
        } else {
            if (!is_numeric($PageSize) || $PageSize < 0)
                $this->PageSize = 40;
             else if ($PageSize == "0")
                $this->PageSize = 100;
             else 
                $this->PageSize = min(100, $PageSize);
        }
        $MinPageSize += $MaxSectionSize;
        if ($this->PageSize && $MinPageSize && $this->PageSize < $MinPageSize)
            $this->PageSize = $MinPageSize;
        $this->PageNumber = $this->ViewMode == "Print" ? 1 : intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0 ) {
            $this->PageNumber = 1;
        }
        $this->SorterName = CCGetParam("Report1Order", "");
        $this->SorterDirection = CCGetParam("Report1Dir", "");

        $this->Sorter_id = & new clsSorter($this->ComponentName, "Sorter_id", $FileName, $this);
        $this->Sorter_nombre = & new clsSorter($this->ComponentName, "Sorter_nombre", $FileName, $this);
        $this->Sorter_anio = & new clsSorter($this->ComponentName, "Sorter_anio", $FileName, $this);
        $this->Sorter_mes = & new clsSorter($this->ComponentName, "Sorter_mes", $FileName, $this);
        $this->id = & new clsControl(ccsReportLabel, "id", "id", ccsInteger, "", "", $this);
        $this->nombre = & new clsControl(ccsReportLabel, "nombre", "nombre", ccsText, "", "", $this);
        $this->anio = & new clsControl(ccsReportLabel, "anio", "anio", ccsInteger, "", "", $this);
        $this->mes = & new clsControl(ccsReportLabel, "mes", "mes", ccsInteger, "", "", $this);
        $this->NoRecords = & new clsPanel("NoRecords", $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpSimple, $this);
        $this->Navigator->PageSizes = array("1", "5", "10", "25", "50");
    }
//End Class_Initialize Event

//Initialize Method @3-6C59EE65
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = $this->PageSize;
        $this->DataSource->AbsolutePage = $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//CheckErrors Method @3-CE67F6FF
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->id->Errors->Count());
        $errors = ($errors || $this->nombre->Errors->Count());
        $errors = ($errors || $this->anio->Errors->Count());
        $errors = ($errors || $this->mes->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//GetErrors Method @3-B9F711FD
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->nombre->Errors->ToString());
        $errors = ComposeStrings($errors, $this->anio->Errors->ToString());
        $errors = ComposeStrings($errors, $this->mes->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

//Show Method @3-330A3B5D
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $ShownRecords = 0;

        $this->DataSource->Parameters["urls_anio"] = CCGetFromGet("s_anio", NULL);
        $this->DataSource->Parameters["urls_mes"] = CCGetFromGet("s_mes", NULL);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $this->DataSource->Prepare();
        $this->DataSource->Open();

        $Groups = new clsGroupsCollectionReport1($this);
        $Groups->PageSize = $this->PageSize > 0 ? $this->PageSize : 0;

        $is_next_record = $this->DataSource->next_record();
        $this->IsEmpty = ! $is_next_record;
        while($is_next_record) {
            $this->DataSource->SetValues();
            $this->id->SetValue($this->DataSource->id->GetValue());
            $this->nombre->SetValue($this->DataSource->nombre->GetValue());
            $this->anio->SetValue($this->DataSource->anio->GetValue());
            $this->mes->SetValue($this->DataSource->mes->GetValue());
            if (count($Groups->Groups) == 0) $Groups->OpenGroup("Report");
            $Groups->AddItem();
            $is_next_record = $this->DataSource->next_record();
        }
        if (!count($Groups->Groups)) 
            $Groups->OpenGroup("Report");
        else
            $this->NoRecords->Visible = false;
        $Groups->CloseGroup("Report");
        $this->TotalPages = $Groups->TotalPages;
        $this->TotalRows = $Groups->TotalRows;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) return;

        $this->Attributes->Show();
        $ReportBlock = "Report " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $ReportBlock;

        if($this->CheckErrors()) {
            $Tpl->replaceblock("", $this->GetErrors());
            $Tpl->block_path = $ParentPath;
            return;
        } else {
            $items = & $Groups->Groups;
            $i = $Groups->Pages[min($this->PageNumber, $Groups->TotalPages) - 1];
            $this->ControlsVisible["id"] = $this->id->Visible;
            $this->ControlsVisible["nombre"] = $this->nombre->Visible;
            $this->ControlsVisible["anio"] = $this->anio->Visible;
            $this->ControlsVisible["mes"] = $this->mes->Visible;
            do {
                $this->Attributes->RestoreFromArray($items[$i]->Attributes);
                $this->RowNumber = $items[$i]->RowNumber;
                switch ($items[$i]->GroupType) {
                    Case "":
                        $Tpl->block_path = $ParentPath . "/" . $ReportBlock . "/Section Detail";
                        $this->id->SetValue($items[$i]->id);
                        $this->id->Attributes->RestoreFromArray($items[$i]->_idAttributes);
                        $this->nombre->SetValue($items[$i]->nombre);
                        $this->nombre->Attributes->RestoreFromArray($items[$i]->_nombreAttributes);
                        $this->anio->SetValue($items[$i]->anio);
                        $this->anio->Attributes->RestoreFromArray($items[$i]->_anioAttributes);
                        $this->mes->SetValue($items[$i]->mes);
                        $this->mes->Attributes->RestoreFromArray($items[$i]->_mesAttributes);
                        $this->Detail->CCSEventResult = CCGetEvent($this->Detail->CCSEvents, "BeforeShow", $this->Detail);
                        $this->Attributes->Show();
                        $this->id->Show();
                        $this->nombre->Show();
                        $this->anio->Show();
                        $this->mes->Show();
                        $Tpl->block_path = $ParentPath . "/" . $ReportBlock;
                        if ($this->Detail->Visible)
                            $Tpl->parseto("Section Detail", true, "Section Detail");
                        break;
                    case "Report":
                        if ($items[$i]->Mode == 1) {
                            $this->Report_Header->CCSEventResult = CCGetEvent($this->Report_Header->CCSEvents, "BeforeShow", $this->Report_Header);
                            if ($this->Report_Header->Visible) {
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock . "/Section Report_Header";
                                $this->Attributes->Show();
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock;
                                $Tpl->parseto("Section Report_Header", true, "Section Detail");
                            }
                        }
                        if ($items[$i]->Mode == 2) {
                            $this->Report_Footer->CCSEventResult = CCGetEvent($this->Report_Footer->CCSEvents, "BeforeShow", $this->Report_Footer);
                            if ($this->Report_Footer->Visible) {
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock . "/Section Report_Footer";
                                $this->NoRecords->Show();
                                $this->Attributes->Show();
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock;
                                $Tpl->parseto("Section Report_Footer", true, "Section Detail");
                            }
                        }
                        break;
                    case "Page":
                        if ($items[$i]->Mode == 1) {
                            $this->Page_Header->CCSEventResult = CCGetEvent($this->Page_Header->CCSEvents, "BeforeShow", $this->Page_Header);
                            if ($this->Page_Header->Visible) {
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock . "/Section Page_Header";
                                $this->Attributes->Show();
                                $this->Sorter_id->Show();
                                $this->Sorter_nombre->Show();
                                $this->Sorter_anio->Show();
                                $this->Sorter_mes->Show();
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock;
                                $Tpl->parseto("Section Page_Header", true, "Section Detail");
                            }
                        }
                        if ($items[$i]->Mode == 2 && !$this->UseClientPaging || $items[$i]->Mode == 1 && $this->UseClientPaging) {
                            $this->Navigator->PageNumber = $items[$i]->PageNumber;
                            $this->Navigator->TotalPages = $Groups->TotalPages;
                            $this->Navigator->Visible = ("Print" != $this->ViewMode);
                            $this->Page_Footer->CCSEventResult = CCGetEvent($this->Page_Footer->CCSEvents, "BeforeShow", $this->Page_Footer);
                            if ($this->Page_Footer->Visible) {
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock . "/Section Page_Footer";
                                $this->Navigator->Show();
                                $this->Attributes->Show();
                                $Tpl->block_path = $ParentPath . "/" . $ReportBlock;
                                $Tpl->parseto("Section Page_Footer", true, "Section Detail");
                            }
                        }
                        break;
                }
                $i++;
            } while ($i < count($items) && ($this->ViewMode == "Print" ||  !($i > 1 && $items[$i]->GroupType == 'Page' && $items[$i]->Mode == 1)));
            $Tpl->block_path = $ParentPath;
            $Tpl->parse($ReportBlock);
            $this->DataSource->close();
        }

    }
//End Show Method

} //End Report1 Class @3-FCB6E20C

class clsReport1DataSource extends clsDBConnection1 {  //Report1DataSource Class @3-20EE0F53

//DataSource Variables @3-827D8414
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $wp;


    // Datasource fields
    var $id;
    var $nombre;
    var $anio;
    var $mes;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-DE73767F
    function clsReport1DataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Report Report1";
        $this->Initialize();
        $this->id = new clsField("id", ccsInteger, "");
        
        $this->nombre = new clsField("nombre", ccsText, "");
        
        $this->anio = new clsField("anio", ccsInteger, "");
        
        $this->mes = new clsField("mes", ccsInteger, "");
        

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @3-3CFF5478
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_id" => array("id", ""), 
            "Sorter_nombre" => array("nombre", ""), 
            "Sorter_anio" => array("anio", ""), 
            "Sorter_mes" => array("mes", "")));
    }
//End SetOrder Method

//Prepare Method @3-65A178B5
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_anio", ccsText, "", "", $this->Parameters["urls_anio"], 0, false);
        $this->wp->AddParameter("2", "urls_mes", ccsInteger, "", "", $this->Parameters["urls_mes"], 0, false);
    }
//End Prepare Method

//Open Method @3-BFA14A9D
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "select a.id,a.nombre,a.anio,a.mes from\n" .
        "(\n" .
        "select a.id,a.nombre,an.anio,an.mes\n" .
        "from alumno a ,anios an\n" .
        "where\n" .
        "a.activo = 1 AND\n" .
        "CONCAT( an.anio, lpad(an.mes,2,'0') ) <=  concat(year(now()),lpad(month(now()),2,'0'))\n" .
        "and CONCAT( an.anio, lpad(an.mes,2,'0') ) >= concat(year(a.fechaalta),LPAD( MONTH( a.fechaalta ) , 2,  '0' ))\n" .
        "ORDER BY a.id, an.anio, an.mes\n" .
        ") a\n" .
        "left join\n" .
        "(\n" .
        "select al.id,al.nombre,m.mes,m.anio from alumno al\n" .
        "LEFT JOIN movimientofacturacion m ON ( al.id = m.idAlumno )\n" .
        "where\n" .
        "CONCAT( m.anio, lpad(m.mes,2,'0') ) <=  concat(year(now()),lpad(month(now()),2,'0'))\n" .
        "ORDER BY al.id, m.anio, m.mes\n" .
        ") b\n" .
        "on (a.id = b.id and a.anio= b.anio and a.mes = b.mes)\n" .
        "WHERE\n" .
        "b.id is null\n" .
        "and a.anio = " . $this->SQLValue($this->wp->GetDBValue("1"), ccsText) . "\n" .
        "and a.mes = " . $this->SQLValue($this->wp->GetDBValue("2"), ccsInteger) . "\n" .
        "";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-F166B9D8
    function SetValues()
    {
        $this->id->SetDBValue(trim($this->f("id")));
        $this->nombre->SetDBValue($this->f("nombre"));
        $this->anio->SetDBValue(trim($this->f("anio")));
        $this->mes->SetDBValue(trim($this->f("mes")));
    }
//End SetValues Method

} //End Report1DataSource Class @3-FCB6E20C

class clsRecordReport2 { //Report2 Class @22-40449E32

//Variables @22-D6FF3E86

    // Public variables
    var $ComponentType = "Record";
    var $ComponentName;
    var $Parent;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $ErrorBlock;
    var $FormSubmitted;
    var $FormEnctype;
    var $Visible;
    var $IsEmpty;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    var $InsertAllowed = false;
    var $UpdateAllowed = false;
    var $DeleteAllowed = false;
    var $ReadAllowed   = false;
    var $EditMode      = false;
    var $ds;
    var $DataSource;
    var $ValidatingControls;
    var $Controls;
    var $Attributes;

    // Class variables
//End Variables

//Class_Initialize Event @22-4836253B
    function clsRecordReport2($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record Report2/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "Report2";
            $this->Attributes = new clsAttributes($this->ComponentName . ":");
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->Button_DoSearch = & new clsButton("Button_DoSearch", $Method, $this);
            $this->s_anio = & new clsControl(ccsTextBox, "s_anio", "s_anio", ccsInteger, "", CCGetRequestParam("s_anio", $Method, NULL), $this);
            $this->s_mes = & new clsControl(ccsListBox, "s_mes", "s_mes", ccsInteger, "", CCGetRequestParam("s_mes", $Method, NULL), $this);
            $this->s_mes->DSType = dsTable;
            $this->s_mes->DataSource = new clsDBConnection1();
            $this->s_mes->ds = & $this->s_mes->DataSource;
            $this->s_mes->DataSource->SQL = "SELECT * \n" .
"FROM meses {SQL_Where} {SQL_OrderBy}";
            list($this->s_mes->BoundColumn, $this->s_mes->TextColumn, $this->s_mes->DBFormat) = array("nro", "nombre", "");
        }
    }
//End Class_Initialize Event

//Validate Method @22-28D8FDD4
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->s_anio->Validate() && $Validation);
        $Validation = ($this->s_mes->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->s_anio->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_mes->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @22-87F853AD
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->s_anio->Errors->Count());
        $errors = ($errors || $this->s_mes->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//MasterDetail @22-ED598703
function SetPrimaryKeys($keyArray)
{
    $this->PrimaryKeys = $keyArray;
}
function GetPrimaryKeys()
{
    return $this->PrimaryKeys;
}
function GetPrimaryKey($keyName)
{
    return $this->PrimaryKeys[$keyName];
}
//End MasterDetail

//Operation Method @22-A91567E7
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        if(!$this->FormSubmitted) {
            return;
        }

        if($this->FormSubmitted) {
            $this->PressedButton = "Button_DoSearch";
            if($this->Button_DoSearch->Pressed) {
                $this->PressedButton = "Button_DoSearch";
            }
        }
        $Redirect = "cuotas_impagas.php";
        if($this->Validate()) {
            if($this->PressedButton == "Button_DoSearch") {
                $Redirect = "cuotas_impagas.php" . "?" . CCMergeQueryStrings(CCGetQueryString("Form", array("Button_DoSearch", "Button_DoSearch_x", "Button_DoSearch_y")));
                if(!CCGetEvent($this->Button_DoSearch->CCSEvents, "OnClick", $this->Button_DoSearch)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @22-75B66346
    function Show()
    {
        global $CCSUseAmp;
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->s_mes->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->s_anio->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_mes->Errors->ToString());
            $Error = ComposeStrings($Error, $this->Errors->ToString());
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", !$CCSUseAmp ? $this->HTMLFormAction : str_replace("&", "&amp;", $this->HTMLFormAction));
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        $this->Attributes->Show();
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->Button_DoSearch->Show();
        $this->s_anio->Show();
        $this->s_mes->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End Report2 Class @22-FCB6E20C

//Initialize Page @1-B23BAFB2
// Variables
$FileName = "";
$Redirect = "";
$Tpl = "";
$TemplateFileName = "";
$BlockToParse = "";
$ComponentName = "";
$Attributes = "";

// Events;
$CCSEvents = "";
$CCSEventResult = "";

$FileName = FileName;
$Redirect = "";
$TemplateFileName = "cuotas_impagas.html";
$BlockToParse = "main";
$TemplateEncoding = "CP1252";
$ContentType = "text/html";
$PathToRoot = "./";
$Charset = $Charset ? $Charset : "windows-1252";
//End Initialize Page

//Include events file @1-9D9F7543
include_once("./cuotas_impagas_events.php");
//End Include events file

//Before Initialize @1-E870CEBC
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeInitialize", $MainPage);
//End Before Initialize

//Initialize Objects @1-D2494E95
$DBConnection1 = new clsDBConnection1();
$MainPage->Connections["Connection1"] = & $DBConnection1;
$Attributes = new clsAttributes("page:");
$MainPage->Attributes = & $Attributes;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$Report1 = & new clsReportReport1("", $MainPage);
$Report2 = & new clsRecordReport2("", $MainPage);
$MainPage->header = & $header;
$MainPage->Report1 = & $Report1;
$MainPage->Report2 = & $Report2;
$Report1->Initialize();

BindEvents();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize", $MainPage);

if ($Charset) {
    header("Content-Type: " . $ContentType . "; charset=" . $Charset);
} else {
    header("Content-Type: " . $ContentType);
}
//End Initialize Objects

//Initialize HTML Template @1-BBAEC725
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView", $MainPage);
$Tpl = new clsTemplate();
$Tpl->LoadTemplate(PathToCurrentPage . $TemplateFileName, $BlockToParse, "CP1252");
$Tpl->block_path = "/$BlockToParse";
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow", $MainPage);
$Attributes->SetValue("pathToRoot", "");
$Attributes->Show();
//End Initialize HTML Template

//Execute Components @1-ACCF906B
$header->Operations();
$Report2->Operation();
//End Execute Components

//Go to destination page @1-68B2EA9A
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBConnection1->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($Report1);
    unset($Report2);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-B990E1A5
$header->Show();
$Report1->Show();
$Report2->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
if (!isset($main_block)) $main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-78804B81
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBConnection1->close();
$header->Class_Terminate();
unset($header);
unset($Report1);
unset($Report2);
unset($Tpl);
//End Unload Page


?>
