# Students  

(русская версия ниже)

Try this site: http://shinoa.web44.net/

Educational project: you can **browse** list of students, use **search**, add **your** own data, which will give you cookie token, so you could **edit** your 'Student profile' later.

We use mysql/MariaDB with two tables: one holding data about **profiles**, another manipulates **password** hashes.

## Used technologies

1. PHP 7.1.2 + mb_string + simple_xml
2. MariaDB 10.1.21
3. phpmyadmin
4. Composer (все зависимости вы можете найти
5. Twig
5. PHPUnit
6. PHPDoc

## Prerequisites: 

0. Users of linux, pay attention: some php extensions like mb_string or simple_xml are not installed by default. To run this prject you need to install them manually with 
```
$ sudo apt-get install php7.0-mb_string 
```
1. PHP >= 7.0, apache with document root in /public folder.
2. MySQL/MariaDB with two tables: [here you can download needed dumps](http://zalivalka.ru/359753).
3. Composer (all dependencies lie in .json)
4. Twig templating engine
5. PHPUnit
6. PHPDoc 

Now I will say a few words about Project's structure: https://pastebin.com/vmEff7ih

--- 

Посмотри, как работает этот сайт: http://shinoa.web44.net/

Учебный проект: вы можете просматривать список студентов, пользоваться поиском, добавлять свои собственые данные на сайт, тем самым получая "членство", сохраняемое в куки; посредством этого вы можете в дальнейшем редактировать свои данные в удобной форме.

Мы используем для этой цели mysql/MariaDB с двумя таблицами: одна хранит данные о студентах, другая - о хэшах их паролей.

## Использованные технологии

1. PHP 7.1.2 + mb_string + simple_xml
2. MariaDB 10.1.21
3. phpmyadmin
4. Composer
5. PHPUnit
6. PHPDoc

## Зависимости: 

0. Пользователям linux: некоторые расширения php типа mb_string и simple_xml не устанавливаются с sudo apt-get install php7 по умолчанию. Вам может понадобиться установить их вручную, например:
``` 
$ sudo apt-get install php7.0-mb_string 
```
1. PHP >= 7.0, pache с document root в папке /public.
2. MySQL/MariaDB с двумя таблицами: [здесь вы можете их загрузить](http://zalivalka.ru/359753).
3. Composer (все зависимости можете найти в .json)
4. Twig templating engine
4. PHPUnit
5. PHPDoc

Несколько слов о структуре проекта для простоты понимания: https://pastebin.com/qqr8QZ46


