<% if ENV['NANOC_ENV']=='production' && ENV['NANOC_REMOTE'] %>
@ng_app.constant('SERVER_HOST', "//103.248.102.25/index.php" );
<% else %>
@ng_app.constant('SERVER_HOST', "//127.0.0.1:1122" );
<% end %>
