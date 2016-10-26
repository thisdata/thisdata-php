# 0.2.0

  - Add support for two new API endpoints, POST /verify and GET /events
    https://github.com/thisdata/thisdata-ruby/issues/19
    - help.thisdata.com/docs/apiv1verify
    - help.thisdata.com/docs/v1getevents
    - `ThisData::Event.all` returns a filterable, pageable, list of Events
    - `ThisData.verify(...)` uses ThisData to determine how likely it is that
       the person who is currently logged in has had their account compromised.
    - Includes `thisdata_verify` method in the TrackRequest module, for easier
      use within Rails apps
  - Update POST /events endpoint to accept new parameters like device, session,
    and source details
    - help.thisdata.com/docs/apiv1events
