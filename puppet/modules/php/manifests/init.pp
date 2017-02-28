class php {
  # install order is important to avoid apache2 being installed to provide
  package {
    'php5-cli':
      ensure  => present;
    #require => Exec['apt-update'];
    ['php5-mysql', 'php5-curl', 'php5-fpm', 'php5-gd', 'php5-memcached', 'php5-apcu', 'php-pear', 'php5-xdebug', 'php5-imagick', 'php5-mcrypt']:
      ensure  => present,
      require => Package['php5-cli'];
  }

  service {
    'php5-fpm':
      enable    => true,
      ensure    => running,
      require   => Package['php5-fpm'];
  }

  file {
    '/etc/php5/fpm/php.ini':
      source  => "puppet:///modules/php/php-${env}.ini",
      require => Package['php5-fpm'],
      notify  => Service['php5-fpm'];
    '/etc/php5/fpm/conf.d/apc.ini':
      source  => 'puppet:///modules/php/apc.ini',
      require => Package['php5-fpm'],
      notify  => Service['php5-fpm'];
  }

  if $env == 'dev' {
    file {
      '/etc/php5/fpm/pool.d/www.conf':
      content => template("php/www-${env}.erb"),
      require => Package['php5-fpm'],
      notify  => Service['php5-fpm'];
    }
  }
  elsif $env == 'prod' {
    file {
      '/etc/php5/fpm/pool.d/www1.conf':
        content => template("php/www-${env}-1.erb"),
        require => Package['php5-fpm'],
        notify  => Service['php5-fpm'];
      '/etc/php5/fpm/pool.d/www2.conf':
        content => template("php/www-${env}-2.erb"),
        require => Package['php5-fpm'],
        notify  => Service['php5-fpm'];
    }
  }
}
