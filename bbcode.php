<?php
define("TE_INCLUDEBASE", __DIR__);
require_once "TextEngine/TextEngine.php";
require_once "BBCodeClass/_bbcode.php";
require_once "CustomBBEvulator.php";
$evulator = new BBCodeEvulator();
function BBCodeOrnek()
{
	global $evulator;
	$evulator->SetTagWR("*", new BBCodeInfo("[{%TagName}]{%Text}[/{%TagName%}]"));
	$evulator->SetMultipleTagWR(["b", "i", "u", "s"], new BBCodeInfo("<{%TagName}>{%Text}</{%TagName}>"));
	$bburl = new BBCodeInfo("<a href=\"{%TagAttrib}\">{%Text}</url>");
	$bburl->SetValidator(
                function(&$validator, &$tag)
                {
                    $attr = $validator->TagData["TagAttrib"];
                    if($attr == "@cw")
                    {
                        $validator->TagData["TagAttrib"] = "http://www.cyber-warrior.org";
                    }
                }
	);
	$evulator->SetTag("url", $bburl);
	$evulator->SetTagWR("img", new BBCodeInfo("<img>{%Text}</img>", BBCodeInfoFlags::InnerTextOnly));
	$evulator->SetTagWR("size", new BBCodeInfo("<font size=\"{%TagAttrib}\">{%Text}</font>"));
	$evulator->SetTagWR("color", new BBCodeInfo("<font color=\"{%TagAttrib}\">{%Text}</font>"));
	$evulator->SetTagWR("font", new BBCodeInfo("<font face=\"{%TagAttrib}\">{%Text}</font>"));
	
	//Eval fonksiyonu çağırılıp, parformat kullanılmaz.
	$evulator->SetTagWR("quote", new BBCodeInfo('<blockquote>$Text</blockquote>', 0, true));
	
	$bbcenter = new BBCodeInfo();
	$bbcenter->SetCustomEvulator("CustomBBEvulator");
	$evulator->SetTag("center", $bbcenter);
	
	//satır karakterinin yanına otomatik <br /> eklenir
	$evulator->SetMap("\n", "<br />\r\n");
	//hr tagları otomatik kapatılacak.
	$evulator->SetAutoClosed("hr");
	$evulator->SetTagWR("hr", new BBCodeInfo("<hr />"));
	
	//Color, Font ile kapatılabilir.
	$evulator->SetAlias("color", "font");
	//Size, Font ile kapatılabilir.
	$evulator->SetAlias("size", "font");
	//echo $evulator->EvulateBBCodes("[CENTER]Deneme[/CENTER]");
	echo $evulator->EvulateBBCodes("[COLOR=RED][B]Cyber-Warrior[/B][/FONT] [COLOR=GREEN]AR-GE[/FONT] Grup\n"
	. "[URL=@cw]Cyber-Warrior.Org[/URL]");
	
}
BBCodeOrnek();

