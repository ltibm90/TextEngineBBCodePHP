<?php
class CustomBBEvulator extends BaseEvulator
{
	public function Render(&$tag, &$vars)
	{
		$result = new TextEvulateResult();
		$result->Result = TextEvulateResult::EVULATE_DEPTHSCAN;
		$result->TextContent = "<div style='text-align: center'>";
		return $result;
	}
	public function RenderFinish(&$tag, &$vars, &$latestResult)
	{
		$latestResult->TextContent .= "</div>";
		parent::RenderFinish($tag, $vars, $latestResult);
	}
}
