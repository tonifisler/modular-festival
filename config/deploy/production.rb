set :stage, :production

# Simple Role Syntax
# ==================
#role :app, %w{deploy@example.com}
#role :web, %w{deploy@example.com}
#role :db,  %w{deploy@example.com}

# Extended Server Syntax
# ======================
server 'bassmusik.ch', user: 'bassmusi', roles: %w{web app db}

# you can set custom ssh options
# it's possible to pass any option but you need to keep in mind that net/ssh understand limited list of options
# you can see them in [net/ssh documentation](http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start)
# set it globally
#  set :ssh_options, {
#    keys: %w(~/.ssh/id_rsa),
#    forward_agent: false,
#    auth_methods: %w(password)
#  }

set :application, 'zurichmodular'
set :deploy_to, -> { "/home/bassmusi/www/#{fetch(:application)}" }
set :tmp_dir, "/home/bassmusi/tmp"
SSHKit.config.command_map[:composer] = "php70 /home/bassmusi/composer.phar"

fetch(:default_env).merge!(wp_env: :production)
