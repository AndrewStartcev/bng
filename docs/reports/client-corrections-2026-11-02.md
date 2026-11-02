# BNG corrections deployed on 2 November 2026

The client requested immediate edits on the live NIC installation using the supplied correction document and replacement trench video. The same WordPress installation serves Russian at bng-elements.ru and English at bng-elements.com.

## Published changes

- Home: 2.03 billion tonnes of C1+C2 reserves, GKZ Protocol 6746 dated 21 September 2021, plus 0.9 billion tonnes of P1+P2 resources.
- Rock: approximately 97% composition, Na2O and trace constituents corrected; the deposit description now includes the 120 km all-season road.
- Evidence: the first verified fact specifies the reserve categories and GKZ protocol. The document repeats the heading “Rock” for this screenshot; the actual target is the numbered verified-fact list in Evidence.
- Flowsheet: studies dated 2021–2023; route 03 replaced with the patented company technology and three patent numbers; the selected route is described as combined acid–alkaline. The constraints block was removed and the remaining results use one column.
- Milestones: 10 completed milestones plus 8 future milestones. Added the road/camp milestone for 2017–2019, updated the three field seasons, and included technology development in 2021–2023. The existing future milestones remain intact.
- News: removed the marked introductory paragraph, press explanatory paragraph, and bottom upcoming-events block. The press heading and contact button remain.
- Company: removed the marked subtitle, proof footers below the four principles, and brand architecture section. The principles, company description and contacts remain.
- Equivalent English copy was published through ACF. Shared template removals apply to both domains.

## Replacement video

Published URL: `/wp-content/uploads/2026/11/bng-trench-work-2026-11-muted.mp4` on both domains. Duration 134.68 seconds, 1920 × 1080 H.264, 65,793,996 bytes. The audio track was removed by stream copy; the video was not re-encoded. Both home-page ACF video fields point to the new attachment. The previous attachment and file remain available for rollback.

## Verification

- Opened all eight sections on both domains in the user's browser.
- Verified RU → EN switching preserves the selected section.
- Video plays without a click in RU and EN with autoplay, loop and muted enabled; the prepared file contains only a video stream.
- Both timelines render 18 points across four rows. Desktop layouts have no horizontal overflow. Home video and milestone navigation were checked at a 390-pixel viewport, with no overflow; the viewport was reset afterward.
- Both domains return HTTP 200 for the replacement video and a catalog PDF, with normal TLS verification.
- Existing JavaScript, News/Company, Milestones, Products, Flowsheet and Evidence checks were run. Two old checks required removed blocks to remain visible; their frontend expectations now follow the approved removals while retaining ACF/source integrity checks.

## Recovery and storage

Original field values for Home, Rock, Evidence, Flowsheet and Milestones and the three original HTML templates were saved locally under the task's private directory before editing. Restore the affected fields through WordPress and the three templates through the NIC file manager if rollback is needed. The old video attachment is ID 474.

The adjacent `2026-11-02-content.json` records the published content changes for review; it is not a full database export or an automatic import script. ACF remains the production content source. Removed template sections retain their old ACF data for recovery. Credentials, SQL, private configuration and video binaries are excluded from GitHub.
