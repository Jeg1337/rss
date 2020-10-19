<?php
$c = curl_init('https://news.yandex.ru/Moscow/index.rss');
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HEADER, 0);
$h = curl_exec($c);

$DBHost = "localhost";
	$DBUser = "root";
	$DBPassword = "";
	$DBName = "datanews";
	
	$Link = mysqli_connect($DBHost, $DBUser, $DBPassword); 
	mysqli_select_db($Link, $DBName);
	mysqli_set_charset($Link , 'utf8');
	$Query = "delete from news where 1";
	mysqli_query($Link, $Query);

$len = array();
$title = array();
$time = array();
$description = array();
$link = array();

while (True) {
	if (strpos($h, '<item>') == False) {
		break;
	} else {
		array_push($len, substr($h, strpos($h, '<item>'), (strpos($h, '</item>') - strpos($h, '<item>') + 7 )));
		$h = substr($h, strpos($h, '</item>') + 7);
	}
} 

echo '<table border = 1><tr><td>Заголовок</td><td>Новость</td><td>Время</td><td>Ссылка</td></tr>';

for ($i = 0; $i < count($len); $i++) {
	array_push($title, substr($len[$i], strpos($len[$i], '<title>') + 7, (strpos($len[$i], '</title>') - strpos($len[$i], '<title>') - 7)));
	array_push($description, substr($len[$i], strpos($len[$i], '<description>') + 13, (strpos($len[$i], '</description>') - strpos($len[$i], '<description>') - 13)));
	array_push($time, substr($len[$i], strpos($len[$i], '<pubDate>') + 9, (strpos($len[$i], '</pubDate>') - strpos($len[$i], '<pubDate>') - 14)));
	array_push($link, substr($len[$i], strpos($len[$i], '<link>') + 6, (strpos($len[$i], '</link>') - strpos($len[$i], '<link>') - 6)));
	echo '<tr>';
	echo '<td>' . $title[$i] . '</td>';
	echo '<td>' . $description[$i] . '</td>';
	echo '<td>' . $time[$i] . '</td>';
	echo "<td><a href = '" . $link[$i] . "'>Ссылка</a></td>";
	echo '</tr>';
	$Query = "insert into news
	values(0, '" . $title[$i] . "', '" . $description[$i] . "', '" . $time[$i] . "', '" .$link[$i] . "')";
	mysqli_query($Link, $Query);
}
echo '</table>';

curl_close($c);
mysqli_close($Link);
?>