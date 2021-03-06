<?php
class BBCodeEvulator
{
	private $BBCodes = array();
	private $evulator;
	/// <summary>
	/// Birden fazla taga bir öğreyi bbkoda bağlar
	/// </summary>
	/// <param name="bbcodes">BBKod dizisi</param>
	/// <param name="info">BBKod bilgisi</param>
	public function SetMultipleTag($bbcodes,  &$info)
	{
		for ($i = 0; $i < count($bbcodes); $i++)
		{
			$this->SetTag($bbcodes[$i], $info);
		}
	}
	public function SetMultipleTagWR($bbcodes, $info)
	{
		$this->SetMultipleTag($bbcodes, $info);
	}
	/// <summary>
	/// Bir tagı bbkoda bağlar
	/// </summary>
	/// <param name="bbcode">BBKod</param>
	/// <param name="info">BBKod bilgisi</param>
	public function SetTag($bbcode, &$info)
	{
		$this->BBCodes[strtolower($bbcode)] = &$info;
	}
	
	public function SetTagWR($bbcode, $info)
	{
		$this->SetTag($bbcode, $info);
	}
	public function __construct()
	{
		$this->evulator = new TextEvulator();
		//Mevcut evulatör tipleri ve tag ayarlamaları silindi.
		$this->evulator->EvulatorTypes->Clear();
		$this->evulator->EvulatorTypes->Param = null;
		$this->evulator->TagInfos->Clear();
		//Mevcut sınıfımız evulator ile birlikte gönderildi.
		$this->evulator->CustomDataSingle = &$this;

		//Tüm tag açılışda doğrudan kapatma eylemi devredışı bırakıldı e.g [TEST /] gibi.
		$this->evulator->TagInfos["*"]->Flags = TextElementFlags::TEF_DisableLastSlash;
		$this->evulator->EvulatorTypes->GeneralType = "BBCodeGeneralEvulator";
		$this->evulator->LeftTag = '[';
		$this->evulator->RightTag = ']';
		$this->evulator->SurpressError = true;
		$this->evulator->AllowCharMap = true;
	}
	/// <summary>
	/// BBKodu belirtilen formata göre değerlendirir.
	/// </summary>
	/// <param name="bbcodetext">Çevirilecek BBKod</param>
	/// <returns></returns>
	public function EvulateBBCodes($bbcodetext)
	{
		$this->evulator->Text = $bbcodetext;
		$this->evulator->Elements->SubElements->Clear();
		$this->evulator->Parse();
		$result = $this->evulator->Elements->EvulateValue();
		if($result) return $result->TextContent;
		return null;
	}
	/// <summary>
	/// Herhangi bir takın belirtilen Tag ile kapatılmasını sağlar.
	/// </summary>
	/// <param name="name">Kaynak</param>
	/// <param name="target">Hedef</param>
	public function SetAlias($name, $target)
	{
		$this->evulator->Aliasses[$name] = $target;
	}
	/// <summary>
	/// Belirtilen BBKod bilgisini dönderir.
	/// </summary>
	/// <param name="bbcode">BBKod</param>
	/// <returns>Yoksa varsayılan veya null döner</returns>
	public function &GetTag($bbcode)
	{
		if(isset($this->BBCodes[strtolower($bbcode)])) return $this->BBCodes[strtolower($bbcode)];
		if(isset($this->BBCodes["*"])) return $this->BBCodes["*"];
		return null;
	}
	public function SetAutoClosed($tagname)
	{
		$this->evulator->TagInfos[$tagname]->Flags |= TextElementFlags::TEF_AutoClosedTag;
	}
	public function SetMap($cur, $target)
	{
		$this->evulator->CharMap[$cur] = $target;
	}
}
