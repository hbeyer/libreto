<?php

function makeDoughnutPageContent($data, $facets, $folder) {
	include('fieldList.php');
	$facets = array_intersect($facets, $doughnutFields);
	$firstFacet = assignFirstFacet($facets);
	$content = '			
			<div class="row">
				<div class="buttonsTop">
					'.makeDoughnutButtons($facets, $firstFacet).'
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<canvas id="myDoughnutChart" width="400" height="400"></canvas>
				</div>
				<div class="col-sm-6">
					<div id="chart-legend" class="chart-legend"></div>
				</div>
			</div>';
		$content .= makeDoughnutScript($data, $facets, $firstFacet, $folder);
	return($content);
}

function makeDoughnutPageContentOld($data, $facets, $folder) {
	include('fieldList.php');
	$facets = array_intersect($facets, $doughnutFields);
	$firstFacet = assignFirstFacet($facets);
	$content = '			
			<div class="row">
				<div class="col-sm-6">
					<canvas id="myDoughnutChart" width="400" height="400"></canvas>
				</div>
				<div class="col-sm-6">
					'.makeDoughnutButtons($facets, $firstFacet).'
					<div id="chart-legend" class="chart-legend"></div>
				</div>
			</div>';
		$content .= makeDoughnutScript($data, $facets, $firstFacet, $folder);
	return($content);
}

function makeDoughnutScript($data, $facets, $firstFacet, $folder) {
	$content = '
			<script type="text/javascript">
';
	foreach($facets as $facet) {
		$json = makeDoughnutJSON($data, $facet, $folder);
		$json = addslashes($json);
		$content .= '				var '.$facet.' = \''.$json.'\';
';
	}
	$content .= '
				var optionsDoughnut = {
					segmentShowStroke: false,
					animateRotate: true,
					animateScale: false,
					tooltipTemplate: "<%= label %>: <%= value %>",
					legendTemplate: "<ul class=\"chart-legend\"><% for (var i=0; i<data.length; i++){%><li><span style=\"background-color:<%=data[i].color%>\"></span><%if(data[i].label){%><%=data[i].label%><%}%></li><%}%></ul>"
				}
		
				var data = JSON.parse('.$firstFacet.');

				var ctx = document.getElementById("myDoughnutChart").getContext("2d");
				var myNewChart = new Chart(ctx).Doughnut(data, optionsDoughnut);
				document.getElementById("chart-legend").innerHTML = myNewChart.generateLegend();
			</script>
';
	return($content);
}


function makeDoughnutJSON($data, $field, $folder) {
	$doughnutArrays = makeCloudArrays($data, $field);
	$doughnutObject = fillDoughnutList($doughnutArrays['weightArray'], $doughnutArrays['nameArray']);
	$result = json_encode($doughnutObject);
	return($result);
}

function fillDoughnutList($weightArray, $nameArray) {
	$content = '';
	$count = 0;
	$weightOthers = 0;
	$minWeight = calculateMinWeight($weightArray, 1);
	foreach($weightArray as $id => $weight) {
		$name = $nameArray[$id];
		$color = assignColor($count);
		if($color == 'outOfColors' or $weight < $minWeight) {
			$weightOthers += $weight;
		}
		else {
			$row = array('value' => $weight, 'color' => $color, 'label' => $name);
			$content[] = $row;
		}
		$count++;
	}
	if($weightOthers != 0) {
		$row = array('value' => $weightOthers, 'color' => '#cccccc', 'label' => 'Sonstige');
		$content[] = $row;
	}
	return($content);
}

function calculateMinWeight($weightArray, $percentage) {
	$sum = array_sum($weightArray);
	$minWeightFloat = $sum/100 * $percentage;
	$minWeight = intval($minWeightFloat);
	return($minWeight);
}

function assignColor($count) {
	$colorsMWW = array(
		'#035151', // MWW-grün
		'#a08246', // MWW-gold
		'#002222', // Es folgen alternierende Abstufungen
		'#5c420e',
		'#003a3a',
		'#836528',
		'#126565',
		'#c1a56c',
		'#257878',
		'#ecd5a7',
		'#00b2b2',
		'#808080', // Dunkelgrau
		'#00FF00', // Grün		
		'#00FFFF', // Türkis
		//'#FF0000', // Rot
		//'#0000FF', // Blau
		//'#CC00CC', // Lila
		//'#ffcc00', // Dunkelgelb
		'#33CCFF', // Hellblau
	);
	if(isset($colorsMWW[$count])) {
		return($colorsMWW[$count]);
	}
	else {
		return 'outOfColors';
	}
}

function makeDoughnutButtons($facets, $firstFacet) {
		$content = '';
		foreach($facets as $facet) {
			$status = '';
			if($facet == $firstFacet) {
				$status = ' active';
			}
			$content .= '
				<button type="button" class="btn btn-default'.$status.'" onclick="javascript:replaceChart('.$facet.')">'.translateFieldNamesButtons($facet).'</button>';
		}
		return($content);
}

function assignFirstFacet($facets) {
	$return = '';
	$wishList = array('languages', 'mediaType', 'genre', 'format', 'subject', 'subjectHist');
	foreach($wishList as $wish) {
		if(in_array($wish, $facets)) {
			$return = $wish;
			break;
		}
	}
	if($return == '') {
		$return = $facets[0];
	}
	return($return);
}

?>