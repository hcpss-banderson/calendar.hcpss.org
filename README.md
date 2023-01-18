# calendar.hcpss.org

## Bringing up the project

```bash
# Create the secrets.
cp env.dist .env

# Launch containers.
docker-compose up -d

# Install dependencies.
docker exec calendar_web composer install

# Initialize the database.
docker exec calendar_web ./bin/console doctrine:migrations:migrate

# Add the calendars.
docker exec calendar_web ./bin/console app:calendar:add \
  "Title of the calendar. Like 'HCPSS Events'" \
  "slug for the calendar. Like 'events', 'hcpss', or 'hcpss-all'" \
  "Now add any number of remote ics files." \
  "Add another one here." \
  "Keep adding them..."

# Here is the real example of the Above command.
docker exec calendar_web ./bin/console app:calendar:add \
  "HCPSS System Calendar" \
  hcpss \
  https://www.google.com/calendar/ical/howard.county.public.schools%40gmail.com/public/basic.ics \
  https://www.google.com/calendar/ical/53tttfm4sd0vai54mnrnpn1q5o%40group.calendar.google.com/public/basic.ics \
  https://calendar.google.com/calendar/ical/537on0svjl80bon1j075a8fep0%40group.calendar.google.com/public/basic.ics

# Fetch the events from the calendar(s).
docker exec calendar_web ./bin/console app:data:refresh
```

Visit http://localhost:9099/
