package com.example.mindcare.ui.screens

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.Button
import androidx.compose.material3.OutlinedCard
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import com.example.mindcare.ui.components.GradientHeader
import com.example.mindcare.ui.components.ProgressCard

@Composable
fun ProfileScreen(
    innerPadding: PaddingValues,
    onOpenAchievements: () -> Unit,
    onOpenSupportCircle: () -> Unit,
    onOpenCrisisSupport: () -> Unit,
    onLogout: () -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(innerPadding)
            .verticalScroll(rememberScrollState())
    ) {
        GradientHeader(
            title = "Your MindCare profile",
            subtitle = "Track your streaks, growth, and support.",
            emoji = "👤"
        )

        Spacer(modifier = Modifier.height(16.dp))

        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp)
        ) {
            Text(
                text = "Your stats",
                fontWeight = FontWeight.SemiBold
            )
        }
        Spacer(modifier = Modifier.height(4.dp))
        ProgressCard(
            title = "Check‑in streak",
            description = "You’ve checked in 3 days in a row. Aim for 7!",
            progress = 0.4f
        )
        ProgressCard(
            title = "Weekly reflection",
            description = "2 of 3 recommended journal reflections completed.",
            progress = 0.66f
        )

        Spacer(modifier = Modifier.height(16.dp))
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp)
        ) {
            Text(
                text = "Growth & support",
                fontWeight = FontWeight.SemiBold
            )
            Spacer(modifier = Modifier.height(8.dp))

            OutlinedCard(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(vertical = 4.dp),
                onClick = onOpenAchievements
            ) {
                Text(
                    text = "Achievements & badges 🏅",
                    modifier = Modifier.padding(16.dp)
                )
            }
            OutlinedCard(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(vertical = 4.dp),
                onClick = onOpenSupportCircle
            ) {
                Text(
                    text = "Support circle 🤝",
                    modifier = Modifier.padding(16.dp)
                )
            }
            OutlinedCard(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(vertical = 4.dp),
                onClick = onOpenCrisisSupport
            ) {
                Text(
                    text = "Crisis support & safety plan 🆘",
                    modifier = Modifier.padding(16.dp)
                )
            }
        }

        Spacer(modifier = Modifier.height(24.dp))
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp)
        ) {
            Text(
                text = "Settings",
                fontWeight = FontWeight.SemiBold
            )
            Spacer(modifier = Modifier.height(8.dp))
            Button(
                onClick = onLogout,
                modifier = Modifier.fillMaxWidth()
            ) {
                Text("Log out")
            }
            Spacer(modifier = Modifier.height(24.dp))
        }
    }
}


