<?php
echo '<span class="label label-primary">'.$actual_language.'</span>';
foreach ($languages as $langCode => $langName)
{
    if ($langCode != $language_code)
    {
        echo '
            <a href="?page='.isset($_GET['page']).'&amp;lang=' . $langCode . '">
                <img src="img/flags/flag-'. $langCode . '.png" alt="' . $langName . '" title="' . $langName . '" />
            </a>
        ';
    }
}
?>
