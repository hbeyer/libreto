<?php

function makeDoughnutPageContent($data, $facets, $folder) {
	include('fieldList.php');
	$number = countCollection($data);
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
					<div class="chart-numbers">Gesamtzahl: '.$number['items'].'<br />B&auml;nde: '.$number['volumes'].'</div>
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
		
		'#046262', // Es folgen alternierende Abstufungen
		'#b1904e',
		
		'#034a4a',
		'#8e733e',
		
		'#047b7b',
		'#b99b5f',
		
		'#023131',
		'#7c6536',
		
		'#059494',
		'#c1a671',
		
		'#011919',
		'#6a562f',
		
		'#06acac',
		'#c9b183',
		
		'#000000',
		'#594827',
		
		'#07c5c5',
		'#d0bc95'
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

function countCollection($data) {
	$items = count($data);
	$volumes = 0;
	foreach($data as $item) {
		//If the item is not a miscellany, the number of volumes is being added.
		if($item->itemInVolume == 0) {
			$volumes += $item->volumes;
		}
		//If the item is part of a miscellany, only the first part of it is being countet
		elseif($item->itemInVolume == 1) {
			$volumes += 1;
		}
	}
	return(array('items' => $items, 'volumes' => $volumes));
}

?>