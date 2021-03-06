# PHPJava
PHPJavaは、コンパイルされたJavaクラスをPHP上でエミュレートするためのプロジェクトです。
JavaクラスはもちろんのことJARにも対応しています。（現在実装中）
PHPJavaを使うことにより、Javaのモジュールを活用した拡張性の高いシステムを構築することを可能とします。

# どういう仕組みなの？

## 仕組みについて
コンパイルされたJavaクラスのバイトコードをPHPJavaが理解し、
オペコードに従い逐次処理していきます。

下記を参考に作りました。

The Java® Virtual Machine Specification : https://docs.oracle.com/javase/specs/jvms/se8/html/

Java Bytecode Instruction Listings : https://en.wikipedia.org/wiki/Java_bytecode_instruction_listings


## 動作について

例:
```php
<?php
$javaClass = new JavaClass('test.class');
$javaClass->getMethodInvoker()->main(array(999, 888));
```

上記のようにして、 `test.class` のmainメソッドを呼び出すことが可能となります。
(mainメソッドの引数はString[]を取るため、配列型を渡しています。)

また、Javaのバイトコードではコンストラクタの定義は`<init>`とされており、PHPで呼び出すのは
`call_user_func`や`$javaClass->{'<init>'}()`等としないと表現が難しいですが、
PHPJavaでは、下記の方法でJavaのコンストラクタを簡易的に呼び出す方法を提供しています。

```php
<?php
$javaClass = new JavaClass('test.class');
$invoker = $javaClass->construct();

// 静的なメソッド
$invoker->main(array(999, 888));
// または
$javaClass->getMethodInvoker()->main(array(999, 888));


// 動的なメソッド
$invoker->abc(1234);
```



## メンバの呼び出しについて
PHPJavaではstaticであるかどうかを区別しません。
メンバの呼び出しは非常に単純で、下記のように実行すると、Javaのクラスで定義されているメンバを取得することが可能です。

```php
<?php
$javaClass = new JavaClass('test.class');
$invoker = $javaClass->construct();

// Java側で `String stringValue="Hello World"` と定義されていた場合、
// Hello Worldと出力をします。
var_dump((string) $invoker->stringValue);

// クラスを調べるとjava\lang\Stringという扱いになります。
var_dump(get_class($invoker->stringValue));

// プリミティブな型の場合JavaType*が出力されます。
// Java側で`int intvalue=1111`と定義されていた場合下記の例ではJavaTypeIntが出力されます。
var_dump(get_class($invoker->intValue));

// なお、値を取得すると1111となります。
var_dump((string) $invoker>intValue);
var_dump($invoker->intValue->getValue());

```


## 型の定義について
PHPJavaでは、呼び出すメソッドが静的、あるいは動的かを区別しません。
これは、PHPで簡易的にJavaのメソッドを呼び出すためです。

また、PHPは動的型付けのため、PHPJavaではJavaへの引数においても厳密に比較しません。
したがって、Javaへ引数を渡す際には暗黙の型変換が行われます。

また、PHPのint型の最大値が32bit及び64bit環境により異なることから、
巨大な数字の計算時には`bcmath関数`または `gmp関数` を用いて計算を行います。
(優先的に`bcmath関数`を使用するようにしており利用できなければ、`gmp関数`を使用するようにしています。)

bcmath関数: http://php.net/manual/ja/book.bc.php

gmp関数: http://php.net/manual/ja/ref.gmp.php

なお、下記はJavaのメソッドを実行した際に返却される型の情報です。
null以外の全ての値は`JavaType*`オブジェクトとして適切に扱われます。

|Javaの型        |PHPの型         |
|:-------------:|:-------------:|
|null |null |
|boolean |JavaTypeBoolean |
|char |JavaTypeChar |
|byte |JavaTypeByte |
|short |JavaTypeShort |
|int |JavaTypeInt |
|long |JavaTypeLong |
|float |JavaTypeFloat |
|double |JavaTypeDouble |

## エミュレーションについて

PHPJavaではJavaにデフォルトでビルトインされている機能は一切使用していません。
つまり、PHPJava単体で、Javaをエミュレーションすることが可能となります。



# Javaにおけるスレッドの扱いについて
Javaにはマルチスレッドを扱うための機能が備わっていますが、PHP 5.6現在ではそのような機能はありません。
Javaにおけるスレッドの扱いをPHPJavaで表現するには、pthreads(http://php.net/manual/ja/book.pthreads.php) の導入を必要とします。

# TODO

- 全ニーモニックへの対応
- JARへの対応
- アノテーションへの対応
- PHPDocの追加