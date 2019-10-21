=== SQLite Integration ===
Contributors: kjmtsh
Plugin Name: SQLite Integration
Plugin URI: http://dogwood.skr.jp/wordpress/sqlite-integration-ja/
Tags: database, SQLite, PDO
Author: Kojima Toshiyasu
Author URI: http://dogwood.skr.jp/
Requires at least: 3.3
Tested up to: 4.1.1
Stable tag: 1.8.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SQLite IntegrationはSQLiteでWordPressを使えるようにするプラグインです。

== Description ==

このプラグインを使うと、MySQL データベースサーバがなくても WordPress のサイトを作ることができます。必要なものは、Apache か、同等のウェブサーバと、PDO エクステンションが使える PHP だけです。WordPress のアーカイブとこのプラグインがあれば、それだけで WordPress サイトを試すことができます。

SQLite Integration は [PDO for WordPress](http://wordpress.org/extend/plugins/pdo-for-wordpress) プラグインの後継です。後者は残念なことに、もうメンテナンスされていないようです。SQLite IntegrationはPDO for WordPressの基本的なアイディアと構造を借りて、より多くの機能とユーティリティを追加しました。

= 特徴 =

SQLite Integration はデータベースアクセスを制御するためのプログラムです。だから、他のプラグインとは違います。WordPress をインストールするときに使わなければなりません。インストールのセクションを参照してください。[SQLite Integration(ja)](http://dogwood.skr.jp/wordpress/sqlite-integration-ja/)をご覧になると、もっと詳しい説明を読むことができます。

インストールに成功したら、MySQL を使う他の WordPress と同じように使うことができます。オプションとして、一時的に MySQL を使い、また SQLite に戻るというような使い方ができます。開発のときに、MySQL のないローカルマシンでサイトのテストをすることもできるでしょう。

インストールが終わったら、このプラグインを有効化することができます(必須ではありませんが、お勧めします）。そうすると、サーバやインストールされたプラグインについての情報を見ることができるようになります。

= システム要件 =

* PHP 5.2 以上で PDO extension が必要です(PHP 5.3 以上をお勧めします)。
* PDO SQLite ドライバがロードされている必要があります。

= 後方互換性 =

現在[PDO for WordPress](http://wordpress.org/extend/plugins/pdo-for-wordpress)をお使いの場合は、データベースを移行することができます。インストールのセクションをご覧ください。

= サポート =

下の方法でコンタクトを取ってください。

1. [Support Forum](http://wordpress.org/support/plugin/sqlite-integration)にポストする。
2. [SQLite Integration(ja)のページ](http://dogwood.skr.jp/wordpress/sqlite-integration-ja/)でメッセージを残す。

= サポートについての注意 =

WordPress.org は MySQL 以外のデータベースを正式にサポートしていません。だから、WordPress.org からのサポートは得られません。フォーラムに投稿しても、回答を得ることはまずないでしょう。また、パッチをあてたプラグインを使う場合は、そのプラグインの作者からのサポートはないものと思ってください。できるだけご協力はしますが、自分でリスクを負う必要があります。

= 翻訳 =

ドキュメントは英語で書かれています。もしあなたの言語に翻訳をしたら、知らせてください。

* 日本語(kjmtsh)
* スペイン語(Pablo Laguna)

== インストール ==

より詳細な情報は、[SQLite Integration(ja)](http://dogwood.skr.jp/wordpress/sqlite-integration-ja/)をご覧ください。

= 準備 =

1. 最新の WordPress アーカイブとこのプラグインをダウンロードして、ローカルマシンに展開します。
2. sqlite-integration フォルダを wordpress/wp-content/plugins フォルダに移動します。
3. sqlite-integration フォルダの中にある db.php ファイルを wordpress/wp-content フォルダにコピーします。
4. wordpress/wp-config-sample.php ファイルを wordpress/wp-config.php とリネームします。

= 基本的な設定 =

wp-config.php ファイルを開き、下のセクションを編集します。

* 認証用ユニークキー
* WordPressデータベーステーブルの接頭辞
* ローカル言語(日本語版では、jaがすでに設定されています)

Codex の[wp-config.php の編集](http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86)を参照してください。

= 5 分もかからないインストール =

wordpress フォルダをサーバにアップロードして、ブラウザでアクセスすると、インストールが始まります。ブログ名などを入力してインストールすれば終了です。ブログをお楽しみください。

= オプション設定 =

wp-config.php に設定を書き込むと、デフォルトの設定を変更することができます。SQLite データベースファイル名を変更したい場合は（デフォルトは .ht.sqlite です）、下の 1 行を加えてください。

`define('DB_FILE', 'your_database_name');`

SQLite データベースファイルが置かれるディレクトリを変更したい場合は、次の行を追加してください。

`define('DB_DIR', '/home/youraccount/database_directory/');`

どちらか片方だけでも、両方でも変更できます。

= このプラグインを削除せずに MySQL を使う =

MySQL を使いたいときは、次の行を wp-config.php に追加してください。

`define('USE_MYSQL', true);`

もちろん、これだけでは足りません。データベースサーバやユーザ名、パスワードなども設定してください。この行を追加して、最初にサイトにアクセスすると、WordPress のインストールが始まります。MySQL でのインストールを終えてください。おわかりのように、SQLite のデータは自動的に MySQL に引き継がれることはありません。

SQLite に戻りたいときには、この行を次のように変更するか、この行を削除してください。

`define('USE_MYSQL', false);`

= PDO for WordPress から SQLite Integration にデータベースを移行する =

現在、PDO for WordPress をお使いなら、データベースを SQLite Integration に移行することができます。お勧めは下のようにすることです。より詳細な情報については、[SQLite Integration(ja)](http://dogwood.skr.jp/wordpress/sqlite-integration-ja/)を参照してください。

1. 現在のデータをエクスポートする。
2. 最新のWordPressを、SQLite Integrationを使って新規インストールする。
3. WordPress Importerを使って以前のデータをインポートする。

何らかの理由で、データがエクスポートできない場合は、上のサイトをご覧ください。別の方法を読むことができます。

== よくある質問 ==

= 「データベース接続エラー」でインストールが止まります =

wp-config.php を手動で作成することが必要です。WordPress に作らせようとすると、途中でインストールが止まります。

= データベース・ファイルが作られません =

ディレクトリやファイルを作るのに失敗するのは、多くの場合、PHPにその権限がないことが原因です。サーバの設定を確認するか、管理者に聞いてみてください。

= あれこれのプラグインが有効化できません、あるいはちゃんと動作しません =

ある種のプラグイン、特にキャッシュ系のプラグインやデータベース管理系のプラグインはこのプラグインと一緒に使えません。SQLite Integrationを有効化して、ドキュメントの「プラグイン互換性」のセクションをご覧ください。あるいは、[SQLite Integration Plugin Page](http://dogwood.skr.jp/wordpress/plugins/)をご覧ください。

= 管理画面のドキュメントは必要ないのですが =

無効化すればすぐに消えます。有効化と無効化は管理画面の表示・非表示だけで、本体には影響を与えません。プラグインを削除したい場合は、単に削除すれば消えます。

== Screenshots ==

1. システム情報の画面ではデータベースの状態やプラグインの対応状況を見ることができます。

== Known Limitations ==

多くのプラグインはちゃんと動作するはずです。が、中にはそうでないものもあります。一般的には、WordPressの関数を通さず、PHPのMysqlあるいはMysqliライブラリの関数を使ってデータベースを操作しようとするプラグインは問題を起こすでしょう。

他には下のようなものがあります。

これらのプラグインを使うことはできません。SQLite Integrationと同じファイルを使おうとするからです。

* W3 Total Cache
* DB Cache Reloaded Fix
* HyperDB

'WP Super Cache'や'Quick Cache'のようなプラグインなら使えるかもしれません。お勧めはしませんし、何も保証しませんが。

これらのプラグインも使えません。SQLite Integration がエミュレートできない MySQL 独自の拡張機能を使っているからです。

* Yet Another Related Posts
* Better Related Posts

'WordPress Related Posts'や'Related Posts'のようなプラグインなら使うことができるかもしれません。

たぶん、もっとあるでしょう。動作しないプラグインを見つけたら、お知らせいただけると助かります。

非互換のプラグインの中には、少し修正をすると、使えるようになるものがあります。[Plugins(ja)](http://dogwood.skr.jp/wordpress/plugins-ja/)で情報を公開していますので、参照してください。

このプラグインは、'WP_PLUGIN_URL' 定数をサポートしません。

== Upgrade Notice ==

WordPress 4.1.1 での動作チェックをして、いくつかのバグを修正しました。アップグレードをお勧めします。自動アップグレードで失敗するようなら、FTPを使っての手動アップグレードを試してみてください。

== Changelog ==

= 1.8 (2014-03-06) =
* インストール・プロセスのバグを修正しました。
* index query の正規表現を修正しました。いくつかのプラグインが影響を受けるかもしれません。
* PHP 5.2.x で動作しない部分を修正しました。

= 1.7 (2014-09-05) =
* エディタ画面で、添付ファイルの並べ替えができなかったのを修正しました。
* CREATE クエリのバグを修正しました。
* 128x128 アイコンと 256x256 アイコンを追加しました。
* pcre.backtrack_limit が大きな値に設定されている場合、それを使うようにしました。
* メタクエリの BETWEEN の扱いを変更しました。
* WordPress 4.0 での動作チェックをしました。

= 1.6.3 (2014-05-10) =
* BETWEEN 比較をするメタクエリのバグを修正しました。
* スペイン語カタログを追加しました。
* WordPress 3.9.1 での動作チェックをしました。

= 1.6.2 (2014-05-05) =
* 正規表現に関するバグを修正しました。
* 管理画面のドキュメント（表示されていなかった）を修正しました。

= 1.6.1 (2014-04-22) =
* WP Slimstat を使うために、いくつかのバグを修正しました。
* 古い db.php を使い続けている場合は、ダッシュボードに注意を表示するようにしました（必要な場合のみ）。
* 古い db.php を新しいものを置き換えるユーティリティを追加しました。
* 日本語カタログファイルをアップデートしました。

= 1.6 (2014-04-17) =
* 未対応のクエリに対するエラーメッセージのコントロールができていないのを修正しました。
* SQL_CALC_FOUND_ROW ステートメントのバグを修正しました。メインクエリと WP_Query、WP_Meta_Query などのページング情報に関連したものです。
* コメント本文からバッククォートが削除されるバグを修正しました。
* バックアップファイルをローカルにダウンロードできるようにしました。
* PHP documentor で使えるように、ソースコードのドキュメントを書き直しました。
* このドキュメントを変えました。
* マイナーな変更、修正がいくつかありました。
* WordPress 3.8.2 と 3.9 alpha でインストールテストをしました。
* プラグイン互換リストを増補しました。
* これまで使えなくしていた wp-db.php の関数を使えるようにしました。
* いくつかのユーザ定義関数を追加しました。

= 1.5 (2013-12-17) =
* WordPress 3.8 でのインストールと動作テストをしました。
* SQLite と MySQL を交互に使えるようにしました。
* readme-ja.txt のインストールの説明をかえました。
* SQLite のコンパイルオプション'ENABLE_UPDATE_DELETE_LIMIT'をチェックするようにしました。
* WordPress 3.8 の管理画面に合わせて sytle を変更しました。
* グローバルで動くファイルへのダイレクトアクセスを制限するようにしました。

= 1.4.2 (2013-11-06) =
* ダッシュボードに表示される情報についてのバグを修正しました。
* スクリーンショットを変更しました。
* WordPress 3.7.1 でのインストールテストを行いました。

= 1.4.1 (2013-09-27) =
* BETWEEN関数の書き換え方を修正しました。致命的なバグです。新規投稿に'between A and B'というフレーズが含まれていると、公開されず、投稿自体も消えます。
* MP6を使っているときに、管理画面のレイアウトが崩れるのを修正しました。
* 日本語が一部表示されないのを直しました。
* SELECT version()がダミーデータを返すようにしました。
* WP_DEBUGが有効の時に、WordPressのテーブルからカラム情報を読んで表示できるようにしました。

= 1.4 (2013-09-12) =
* アップグレードしたWordPressで期待通り動作しないのを修正するために、データベース管理ユーティリティを追加しました。
* SHOW INDEXクエリにWHERE句がある場合の処理を変更しました。
* ALTER TABLEクエリのバグを修正しました。

= 1.3 (2013-09-04) =
* データベースファイルのスナップショットをzipアーカイブとしてバックアップするユーティリティを追加しました。
* ダッシュボードのスタイルをMP6プラグインに合わせたものに変えました。
* 言語カタログが読み込まれていないときのエラーメッセージの出力方法を一部変更しました。
* query_create.class.phpの_rewrite_field_types()を変更しました。dbDelta()関数が意図したとおりに実行されます。
* BETWEENステートメントが使えるようになりました。
* クエリからインデックスヒントを全て削除して実行するようにしました。
* New StatPressプラグインが使えるように、ALTER TABLE CHANGE COLUMNの扱いを修正しました。
* いくつかの小さなバグを修正しました。

= 1.2.1 (2013-08-04) =
* wp-db.phpの変更にともなって、wpdb::real_escapeプロパティを削除しました。WordPress 3.6 との互換性を保つための変更です。

= 1.2 (2013-08-03) =
* カレンダー・ウィジェットでの不具合に対応するため、日付フォーマットとそのクオートを修正しました。
* Windows マシンでパッチファイルが削除できなかったのを修正しました。
* パッチファイルをアップロードするときに textdomain のエラーが出るのを修正しました。
* ON DUPLICATE KEY UPDATEをともなったクエリの処理を変更しました。
* readme.txt と readme-ja.txt の間違いを直しました。

= 1.1 (2013-07-24) =
* DROP INDEX 単独のクエリが動作していなかったのを修正しました。
* shutdown_hook で destruct() を実行していたのをやめました。
* LOCATE() 関数を使えるようにしました。

= 1.0 (2013-07-07) =
* 最初のリリース。
