<?php
  set_time_limit(0);

  define('__ROOT__', dirname(__FILE__) . '/..');
  require_once(__ROOT__.'/config/mysql.php');
  require_once(__ROOT__.'/lib/common.php');
  require_once(__ROOT__.'/lib/known_ips.php');
  require_once(__ROOT__.'/lib/check.php');

  mb_internal_encoding("UTF-8");

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    
    $response = checkHost(trim($data['host']));

    echo json_encode($response);
    exit();
  }

  $locale = 'en';
  if (in_array($_SERVER['HTTP_HOST'], array(
    'xn----7sbadfc3akrj0ahcdz.xn--p1ai',
    'www.xn----7sbadfc3akrj0ahcdz.xn--p1ai',
    'isitblockedinrussia.ru',
    'www.isitblockedinrussia.ru'
  ))) {
    $locale = 'ru';
  }

  $langs = array(
    'en' => array(
      'title' => 'Is It Blocked In Russia?',
      'desc' => 'Check if a site is currently blocked in Russian Federation',
      'placeholder' => 'Enter domain name or IP…',

      'no_prefix' => 'No, ',
      'no_suffix' => ' is probably not blocked in Russia. Yet.',
      'maybe_prefix' => 'Maybe. It appears that ',
      'maybe_suffix' => ' is at least partially blocked in Russia.',
      'yes_prefix' => 'Yes! It appears that ',
      'yes_suffix' => ' is currently blocked in Russia.',

      'ip6_prefix' => 'No, ',
      'ip6_suffix' => ' is an IPv6 address, and they are not blocked in Russia. Yet.',

      'invalid_prefix' => 'Unable to resolve ',
      'invalid_suffix' => '. Please enter a valid domain name or IP address.',

      'details' => 'Details:',
      'domain' => 'Domain',

      'date_prefix' => 'Blocked on ',
      'decision_prefix' => 'Decision ',
      'decision_department' => ' made by ',
      'ip_prefix' => 'This block affects IP',
      'ip_plural' => 's',
      'and' => ' and ',
      'domain_prefix' => 'domain',
      'domain_plural' => 's',
      'url_prefix' => 'URL',
      'url_singular' => '',
      'url_plural' => 's',
    ),
    'ru' => array(
      'title' => 'Заблокировано в РФ',
      'desc' => 'Проверка сайтов на доступность на территории Российской Федерации',
      'placeholder' => 'Введите доменное имя или IP-адрес…',

      'no_prefix' => 'Нет, по всей видимости, ',
      'no_suffix' => ' не блокируется в России. Пока.',
      'maybe_prefix' => 'Возможно. Судя по всему, ',
      'maybe_suffix' => ' по крайней мере частично заблокирован в России.',
      'yes_prefix' => 'Да! Вероятнее всего, ',
      'yes_suffix' => ' заблокирован на территории РФ.',

      'ip6_prefix' => 'Нет, ',
      'ip6_suffix' => ' — это адрес IPv6, а они не блокируются в России. Пока.',

      'invalid_prefix' => 'Не удалось определить адрес ',
      'invalid_suffix' => '. Пожалуйста, введите корректный домен или IP-адрес.',

      'details' => 'Подробности:',
      'domain' => 'Домен',

      'date_prefix' => 'Заблокировано ',
      'decision_prefix' => 'Решение ',
      'decision_department' => ', орган: ',
      'ip_prefix' => 'Эта блокировка затрагивает IP-адрес',
      'ip_plural' => 'а',
      'and' => ' и ',
      'domain_prefix' => 'домен',
      'domain_plural' => 'ы',
      'url_prefix' => 'ссылк',
      'url_singular' => 'у',
      'url_plural' => 'и',
    ),
  );
  $lang = $langs[$locale];

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $lang['title'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $lang['desc'] ?>">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:title" content="<?= $lang['title'] ?>">
    <meta property="og:image" content="hhttps://vk.com/images/gift/952/512.png">
    <meta property="og:site_name" content="<?= $lang['title'] ?>">
    <meta property="og:description" content="<?= $lang['desc'] ?>">
    <meta property="twitter:title" content="<?= $lang['title'] ?>">
    <meta property="twitter:image" content="https://vk.com/images/gift/952/512.png">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.42/css/uikit.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.42/js/uikit.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.42/js/uikit-icons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>
    <style>
      [v-cloak] {
        display: none;
      }
      .uk-h2 b {
        word-wrap: break-word;
      }
      .loader {
        text-align: center;
      }
      .loader span {
        display: inline-block;
        margin: 20px auto;
      }
    </style>
  </head>
  <body>
    <div class="uk-section" id="app">
      <div class="uk-container">
        <h1 class="uk-heading-primary"><?= $lang['title'] ?></h1>
        <div class="uk-text-lead"></div>
        <p>
          <form method="get">
            <input type="text" name="host" v-model="host" ref="host" class="uk-input uk-form-large" placeholder="<?= $lang['placeholder'] ?>" @input="updateHost()" @keydown.enter.prevent="checkHost()"/>
          </form>
        </p>
        
        <div class="loader" v-if="loading" v-cloak>
          <span><i class="fas fa-circle-notch fa-spin fa-4x"></i></span>
        </div>

        <div v-if="checked && results" v-cloak>
          <div v-if="results.error">
            <h2 class="uk-h2 uk-text-success"><?= $lang['ip6_prefix'] ?><b>{{ host }}</b><?= $lang['ip6_suffix'] ?></h2>
          </div>
          <div v-else-if="results.ips.length">
            <h2 class="uk-h2 uk-text-danger" v-if="totallyBlocked"><?= $lang['yes_prefix'] ?><b>{{ host }}</b><?= $lang['yes_suffix'] ?></h2>
            <h2 class="uk-h2 uk-text-warning" v-else-if="partiallyBlocked"><?= $lang['maybe_prefix'] ?><b>{{ host }}</b><?= $lang['maybe_suffix'] ?></h2>
            <h2 class="uk-h2 uk-text-success" v-else><?= $lang['no_prefix'] ?><b>{{ host }}</b><?= $lang['no_suffix'] ?></h2>

            <h4 class="uk-heading-line"><span><?= $lang['details'] ?></span></h4>

            <table>
              <tbody>
                <tr v-if="results.url">
                  <td valign="top" style="width: 100px"><b>URL</b></td>
                  <td valign="top">
                    <span v-if="results.url.blocked.length"><i class="uk-text-danger fas fa-times-circle"></i></span>
                    <span v-else><i class="uk-text-success fas fa-check-circle"></i></span>
                  </td>
                  <td valign="top">
                    {{ results.url.value }}
                    <div v-for="block in results.url.blocked" class="uk-alert uk-alert-danger">
                      <h5><?= $lang['date_prefix'] ?><b>{{ block.decision_date }}</b>. <?= $lang['decision_prefix'] ?><b>{{ block.decision_num }}</b><?= $lang['decision_department'] ?><b>{{ block.decision_org }}</b>.</h5>
                      <p><?= $lang['ip_prefix'] ?>{{ block.ips.length > 1 ? '<?= $lang['ip_plural'] ?>' : '' }} <b>{{ block.ips.join(', ') }}</b><span v-if="block.domains.length">{{ block.urls.length ? ', ' : '<?= $lang['and'] ?>' }} <?= $lang['domain_prefix'] ?>{{ block.domains.length > 1 ? '<?= $lang['domain_plural'] ?>' : '' }} <b>{{ block.domains.join(', ') }}</b></span><span v-if="block.urls.length"><?= $lang['and'] . $lang['url_prefix'] ?>{{ block.urls.length > 1 ? '<?= $lang['url_plural'] ?>' : '<?= $lang['url_singular'] ?>' }} <b>{{ block.urls.join(', ') }}</b></span>.</p>
                    </div>
                  </td>
                </tr>
                <tr v-if="results.domain">
                  <td valign="top" style="width: 100px"><b><?= $lang['domain'] ?></b></td>
                  <td valign="top">
                    <span v-if="results.domain.blocked.length"><i class="uk-text-danger fas fa-times-circle"></i></span>
                    <span v-else><i class="uk-text-success fas fa-check-circle"></i></span>
                  </td>
                  <td valign="top">
                    {{ results.domain.value }}
                    <div v-for="block in results.domain.blocked" class="uk-alert uk-alert-danger">
                      <h5><?= $lang['date_prefix'] ?><b>{{ block.decision_date }}</b>. <?= $lang['decision_prefix'] ?><b>{{ block.decision_num }}</b><?= $lang['decision_department'] ?><b>{{ block.decision_org }}</b>.</h5>
                      <p><?= $lang['ip_prefix'] ?>{{ block.ips.length > 1 ? '<?= $lang['ip_plural'] ?>' : '' }} <b>{{ block.ips.join(', ') }}</b><span v-if="block.domains.length">{{ block.urls.length ? ', ' : '<?= $lang['and'] ?>' }} <?= $lang['host_prefix'] ?>{{ block.domains.length > 1 ? '<?= $lang['host_plural'] ?>' : '' }} <b>{{ block.domains.join(', ') }}</b></span><span v-if="block.urls.length"><?= $lang['and'] . $lang['url_prefix'] ?>{{ block.urls.length > 1 ? '<?= $lang['url_plural'] ?>' : '<?= $lang['url_singular'] ?>' }} <b>{{ block.urls.join(', ') }}</b></span>.</p>
                    </div>
                  </td>
                </tr>
                <tr v-for="ip in results.ips">
                  <td valign="top"><b>IP</b></td>
                  <td valign="top">
                    <span v-if="ip.blocked.length"><i class="uk-text-danger fas fa-times-circle"></i></span>
                    <span v-else><i class="uk-text-success fas fa-check-circle"></i></span>
                  </td>
                  <td valign="top">
                    {{ ip.value }}

                    <div v-for="block in ip.blocked" class="uk-alert uk-alert-danger">
                      <h5><?= $lang['date_prefix'] ?><b>{{ block.decision_date }}</b>. <?= $lang['decision_prefix'] ?><b>{{ block.decision_num }}</b><?= $lang['decision_department'] ?><b>{{ block.decision_org }}</b>.</h5>
                      <p><?= $lang['ip_prefix'] ?>{{ block.ips.length > 1 ? '<?= $lang['ip_plural'] ?>' : '' }} <b>{{ block.ips.join(', ') }}</b><span v-if="block.domains.length">{{ block.urls.length ? ', ' : '<?= $lang['and'] ?>' }} <?= $lang['host_prefix'] ?>{{ block.domains.length > 1 ? '<?= $lang['host_plural'] ?>' : '' }} <b>{{ block.domains.join(', ') }}</b></span><span v-if="block.urls.length"><?= $lang['and'] . $lang['url_prefix'] ?>{{ block.urls.length > 1 ? '<?= $lang['url_plural'] ?>' : '<?= $lang['url_singular'] ?>' }} <b>{{ block.urls.join(', ') }}</b></span>.</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else>
            <h2 class="uk-h2 uk-text-danger">Unable to resolve <b>{{ host }}</b>. Please enter a valid domain name or IP address.</h2>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">


function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};
var app = new Vue({
  el: '#app',
  data: function() {
    return {
      host: '<?= isset($_GET['host']) ? $_GET['host'] : ''; ?>',
      checked: false,
      results: false,
      loading: false,
    }
  },
  computed: {
    partiallyBlocked: function() {
      if (!this.results) {
        return false
      }
      if (this.results.domain && this.results.domain.blocked.length) {
        return true
      }
      for (var i = 0; i < this.results.ips.length; i++) {
        if (this.results.ips[i].blocked.length) {
          return true
        }
      }
      return false
    },
    totallyBlocked: function() {
      if (!this.results) {
        return false
      }
      if (this.results.url && this.results.url.blocked.length) {
        return true
      }
      /*
      if (this.results.domain && this.results.domain.blocked.length) {
        return true
      }
      */
      if (!this.results.ips.length) {
        return false
      }
      for (var i = 0; i < this.results.ips.length; i++) {
        if (!this.results.ips[i].blocked.length) {
          return false
        }
      }
      return true
    }
  },
  methods: {
    updateHost: function() {
      this.checked = false
      this.checkHost()
    },
    checkHost: debounce(function() {
      if (('history' in window) && ('pushState' in window.history)) {
        window.history.pushState({
          host: this.host
        }, '<?= $lang['title'] ?>', this.host.trim() ? '?host=' + encodeURIComponent(this.host) : '')
      }

      this.performCheck()
    }, 800),
    performCheck() {
      if (!this.host.trim().length) {
        this.results = false
        this.checked = true
        return
      }

      this.checked = false
      this.loading = true
      axios.post('/', {
        host: this.host
      }).then(function(response) {
        this.checked = true
        this.loading = false
        this.results = response.data
      }.bind(this))
    }
  },
  mounted: function() {
    this.$refs['host'].focus()
    if (this.host) {
      this.checkHost()
    }
  }
})

if (('history' in window) && ('replaceState' in window.history)) {
  window.history.replaceState({
    host: app.host
  }, '<?= $lang['title'] ?>', app.host.trim() ? '?host=' + encodeURIComponent(app.host) : '')
}

window.onpopstate = function(event) {
  if (event && event.state) {
    app.host = event.state.host
    app.performCheck()
  }
}
    </script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter48601004 = new Ya.Metrika({
                    id:48601004,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/48601004" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117945657-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117945657-1');
</script>
  </body>
</html>