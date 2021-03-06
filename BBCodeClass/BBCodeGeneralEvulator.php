<?php
class BBCodeGeneralEvulator extends BaseEvulator
{
	private $currentInfo = null;
	private $bbCodeEvulator = null;
	private $customEvulatorHandler = null;
	private $printed = false;
	private function &GetDictionary(&$tag)
	{
		$data = array();
		$data["TagAttrib"] = $tag->TagAttrib;
		$data["TagName"] = strtolower($tag->ElemName);
		return $data;
	}
	public function Render(&$tag, &$vars)
	{
		$this->bbCodeEvulator = &$this->Evulator->CustomDataSingle;
		$result = new TextEvulateResult();
		$result->Result = TextEvulateResult::EVULATE_DEPTHSCAN;
		$dict = $this->GetDictionary($tag);
		$this->currentInfo = $this->bbCodeEvulator->GetTag($tag->ElemName);
		if($this->currentInfo == null) return $result;
		if (!empty($this->currentInfo->CustomEvulator))
		{
			
			$this->customEvulatorHandler = new $this->currentInfo->CustomEvulator($this->Evulator);
			$result = $this->customEvulatorHandler->Render($tag, $vars);
		}
		else if (($this->currentInfo->Flags & BBCodeInfoFlags::InnerTextOnly) != 0 && $this->currentInfo->Enabled && !$tag->AutoClosed)
		{
			$dict["Text"] = $tag->InnerText();
			$validateResult = $this->currentInfo->Validate($dict, $tag);
			if($validateResult != null && $validateResult->Cancel)
			{
				$result->TextContent = null;
			}
			else
			{
				if($this->currentInfo->UseEval)
				{
					extract($dict, EXTR_SKIP);
					eval("\$latestResult->TextContent = \"{$this->currentInfo->TagText}\";");
				}
				else
				{
					$result->TextContent = $this->currentInfo->TagFormat->Apply($dict);
				}

			}
			$result->Result = TextEvulateResult::EVULATE_TEXT;
			$this->printed = true;
		}
		return $result;
	}
	public function RenderFinish(&$tag, &$vars, &$latestResult)
	{
		//Render kısmında tamamlandıysa devam etmez.
		if($this->printed)
		{
			parent::RenderFinish($tag, $vars, $latestResult);
			return;
		}
		if ($this->customEvulatorHandler != null)
		{
			$this->customEvulatorHandler->RenderFinish($tag, $vars, $latestResult);
		}
		else if ($this->currentInfo != null && ($this->currentInfo->Flags & BBCodeInfoFlags::InnerTextOnly) == 0 && $this->currentInfo->Enabled)
		{
			$dict = $this->GetDictionary($tag);
			$dict["Text"] = $latestResult->TextContent;
			$validateResult = $this->currentInfo->Validate($dict, $tag);
			if($validateResult != null && $validateResult->Cancel)
			{
				$latestResult->TextContent = null;
			}
			else
			{
				if($this->currentInfo->UseEval)
				{
					extract($dict, EXTR_SKIP);
					eval("\$latestResult->TextContent = \"{$this->currentInfo->TagText}\";");
				}
				else
				{
					$latestResult->TextContent = $this->currentInfo->TagFormat->Apply($dict);
				}
			}
		}
		else
		{
			//default 
			if($tag->AutoClosed)
			{
				$latestResult->TextContent = "[" . $tag->ElemName + "]";
			}
			else
			{
				$latestResult->TextContent = "[" . $tag->ElemName + "]" . $latestResult->TextContent . "[/" + $tag.ElemName . "]";
			}

		}
		parent::RenderFinish($tag, $vars, $latestResult);
	}

}
