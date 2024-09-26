<?php

namespace app\core\helpers;

final class PageViewHelper
{

	public static function countPages(int $countOfItems, int $itemsOnPage) {

		return ceil($countOfItems/$itemsOnPage);

	}

}