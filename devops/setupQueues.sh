
if [ ! -e /etc/supervisor/conf.d/supervisor-webCrawler.conf ]; then
  echo "configure supervisor  ##########################################################"

  sudo cp /home/vagrant/www/funProjects/webCrawler/devops/supervisor-webCrawler.conf /etc/supervisor/conf.d/supervisor-webCrawler.conf
  sudo supervisorctl reread
  sudo supervisorctl update
  sudo supervisorctl start supervisor-tests:*
  sudo supervisorctl start supervisor-urls:*
  sudo supervisorctl start supervisor-htmls:*
  sudo supervisorctl start supervisor-links:*

fi
