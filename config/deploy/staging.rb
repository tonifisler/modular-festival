set :stage, :staging

# Simple Role Syntax
# ==================
# role :app, %w{deploy@example.com}
# role :web, %w{deploy@example.com}
# role :db,  %w{deploy@example.com}

# Extended Server Syntax
# ======================
server 'web420.webfaction.com', user: 'tonifisler', roles: %w{web app db}

set :application, 'modular'
SSHKit.config.command_map[:composer] = "php70 /home/tonifisler/composer.phar"
set :deploy_to, -> { "/home/tonifisler/webapps/#{fetch(:application)}" }
set :tmp_dir, '/home/tonifisler/tmp'

# you can set custom ssh options
# it's possible to pass any option but you need to keep in mind that net/ssh understand limited list of options
# you can see them in [net/ssh documentation](http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start)
# set it globally
 # set :ssh_options, {
 #   forward_agent: true,
 # }

fetch(:default_env).merge!(wp_env: :staging)
