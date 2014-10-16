There are <?= Html::a(sprintf("?%s_search&searchable=%s&search_agent=%d&%s", c2u($searchable), $searchable, $sa->id, $sa->make_search($searchable)), c2up($searchable), array("target" => "_blank")) ?> you might be interested in.
<p> <?= Html::a(sprintf("?search_agent=%d&view", $sa->id), "Your search agent", array("target" => "_blank")) ?>
