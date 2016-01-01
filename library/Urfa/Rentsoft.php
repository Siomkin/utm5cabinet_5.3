<?php

class Urfa_Rentsoft
{
    const VERSION = "3.00";

    /**
     * Called on agent's side.
     * Возвращает тэг IFRAME для вставки в середину страницы Агента.
     *
     * @param string $agName Уникальное название Агента.
     * @param string $agUuid ID пользователя в системе Агента.
     * @param string $agApi Полный URL API, который позволяет работать с этим пользователем.
     * @param string $agSecret Секретный ключ, используется для цифровой подписи.
     * @param string $devDomainSuffix Если задан, добавляется к домену ag.rentsoft.ru.
     * @param int $width Ширина IFRAME (по умолчанию 100%).
     * @return string                   Результирующее значение атрибута src тэга IFRAME.
     */
    public static function getIframe($rsUri, $agRef, $agName, $agUuid, $agApi, $agSecret, $devDomainSuffix = '', $width = null)
    {
        // Full URL of the current page.
        if (!$agRef) {
            $agRef = ($_SERVER['SERVER_PORT'] == 443 ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        // Build and sign response params.
        $rsSignedParams =
            "ag_uuid=" . urlencode($agUuid) . "&" .     // agent's user ID
            "ag_api=" . urlencode($agApi) . "&" .       // agent's API for this user
            "ag_timestamp=" . time() . "&" .            // timestamp of this request creation WITH MILLISECONDS
            "ag_rnd=" . mt_rand();                      // a random number to get unique URL
        $rsSignedParams .= "&ag_sign=" . md5($agSecret . $rsSignedParams);

        // Build response URI.
        $rsResponseUri = '/' . self::VERSION . "/iframe/" . ltrim($rsUri, '/')
            . (false !== strpos($rsUri, '?') ? '&' : '?')
            . $rsSignedParams . "&ag_ref=" . urlencode($agRef);

        // Build IFRAME hostname.
        $rsHostname = "{$agName}.ag.rentsoft.ru{$devDomainSuffix}";

        // Build IFRAME full URL.
        // Note that width must be set by JS together with height due to IE bug.
        $rsSrc = "https://{$rsHostname}" . $rsResponseUri;
        if (!$width) {
            $width = '100%';
        }
        return trim(
            '
            <iframe
                id="rentsoft_ag_iframe"
                scrolling="no"
                src="' . htmlspecialchars($rsSrc) . '"
                style="border:0; padding:0; margin:0; overflow:hidden; height:300px; width:' . $width . '" frameborder="0"
                onload="var th=this; setTimeout(function() {
                    var h=null;
                    if (!h) if (location.hash.match(/^#h(\d+)/)) h=RegExp.$1;
                    if (!h) for (var i=0; i<10000; i+=30) if (top.frames[\'h\'+i]) { h=i; break; }
                    if (h) { th.style.height=parseInt(h)+200+\'px\'; th.style.width=\'' . $width . '\'; }
                }, 10)"
            ></iframe>
        '
        );
    }
}