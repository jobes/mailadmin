<?php
script('mailadmin', 'mailadmin');
style('mailadmin', 'style');
?>
<span id="serverData" data-server="<?php p(json_encode($_['serverData']));?>"></span>
<div id="vue-content"></div>