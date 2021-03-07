<?php
class BBCodeInfo extends PropertyBase
{
	public function __construct($tagText = '', $flags = 0, $useEval = false)
	{
		$this->Enabled = true;
		$this->TagText = $tagText;
		$this->Flags = $flags;
		$this->UseEval = $useEval;
	}
	public function &SetValidator($validator)
	{
		$this->Validator = &$validator;
		return $this;
	}
	public function &SetCustomEvulator($customBBEvulator)
	{
		$this->CustomEvulator = $customBBEvulator;
		return $this;
	}
	/// <summary>
	/// Etkin olup olmadığınıbelirler
	/// </summary>
	public $Enabled;
	public $CustomEvulator;
	public $Flags;
	public $Validator;
	public $UseEval;
	private $tagText;
	private $tagformat;
	public function Get_TagText()
	{
		return $this->tagText;
	}
	public function Set_TagText($value)
	{
		$this->tagText = $value;
		$this->tagformat = null;
	}
	public function &Get_TagFormat()
	{
		if($this->tagformat == null)
		{
			$this->tagformat = new ParFormat($this->TagText);
			$this->tagformat->SurpressError = true;
		}
		return $this->tagformat;
	}
	public function &Validate(&$data, &$tag)
	{
		$validator = null;
		if ($this->Validator == null || !is_callable($this->Validator)) return $validator;
		$validator = new BBCodeValidator();
		$validator->BBCode = &$this;
		$validator->TagData = &$data;
		call_user_func_array($this->Validator, array(&$validator, &$tag));
		return $validator;
	}
}
