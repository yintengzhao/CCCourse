<% if ENV['NANOC_ENV']=='production' %>
<%= JSPipeline.pipe_all_lib(@items, @item) %>
<% end %>
